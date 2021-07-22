<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Helpers\Constants;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Requests\SaleRequest;
use App\Traits\InvoiceOperations;

class SaleController extends Controller
{
    use InvoiceOperations;


    public function index()
    {
        $customer = ['customer' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $withFields = array_merge([], $customer, $warehouse);

        $sales = Sale::with($withFields)->get();

        $sales = $sales->map(function ($sale) {
            return [
                'id'                => $sale->id,
                'reference'         => $sale->reference,
                'customer'          => $sale->customer,
                'warehouse'         => $sale->warehouse,
                'status'            => $sale->status,
                'grand_total'       => $sale->grand_total,
                'paid'              => $sale->paid,
                'due'               => $sale->due,
                'payment_status'    => $sale->payment_status,
            ];
        });

        return $this->success($sales);
    }


    public function create(Request $req)
    {
        $attr = SaleRequest::validationCreate($req);

        $details = &$attr['products'];

        list($isValid, $errMsg) = $this->checkDistinct($details);

        if (!$isValid) return $this->error($errMsg, 422);

        $ids = Arr::pluck($details, 'product_id');

        $products = Product::find($ids);

        list($isValid, $errMsg) = $this->checkProductsWithVariants($details, $products);

        if (!$isValid) return $this->error($errMsg, 422);

        $detailsHasVariants = $this->filterDetailsVariants($details, true);

        if (count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        $sale = Sale::create($attr);

        foreach ($details as &$detail) {
            $detail['sale_id'] = $sale->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        SaleDetail::insert($details);

        if ($req->status === Constants::INVOICE_RECEIVED) {

            list($isValid, $errMsg) = $this->checkingQuantity($details, $products, $variants);

            if (!$isValid) return $this->error($errMsg, 422);

            $this->subtractQuantity($variants, $details, $products);

            $this->updateInstock($products, $variants);

            $this->subtractWarehouseQuantity($req->warehouse_id, $details, $products, 'sale_unit');

        }

        return $this->success([], "The sale invoice has been created successfully");
    }

    /**
        # 1 - Make validation for request update rule
        # 2 - Make Distinct validation for new details
        # 3 - Get Sale with details
        # 4 - Get old details
        # 5 - Set sale id into every new detail
        # 6 - Get all Products by ids
        # 7 - Merge all details to check variants with products
        # 8 - Check Variants with Products => eg. if product has a variant and detail doesn't has a variant,
            the sale was created a long time ago with products doesn't have variants then
            we updated product by added variant to it then we want to update this sale with this product out variant !
            whats happen without this check ?!!
            it will be add quantity into product.instock but product has variant supposed to added quantity into variant.instock
        # 9  - Filter details to get whose has variant
        # 10 - check if new or old status is received and count of detailsHasVariants > 1 to get variants and details to check relations between products with them variants
        # 11 - Sum quantity to old products has not variants and variants by old details
        # 12 - check quantity if the new status is received before taking any action on the quantity to prevent it from begin set instock with a negative number,
            to show what is the broplem happen create sale with any product with quantity 5 and status is received then go to create sale return with the same product
            with quantity 5 and status completed if you check instock of this products will be 0 because be saled and returned it, so if you update sale with any status
            exept received whats happen ?! the product instock now is -5 !!!
        # 13 - Subtract quantity from old products has not variants and variants by old details
        # 14 - delete old details from `sale_details` by $sale->id
        # 15 - if old or new status is received update instock
        # 16 - update sale with new data
        # 17 - insert new details
     */
    public function update(Request $req, $id)
    {
        # [1]
        $attr = SaleRequest::validationUpdate($req);

        $newDetails = &$attr['products'];

        # [2]
        list($isValid, $errMsg) = $this->checkDistinct($newDetails);

        if (!$isValid) return $this->error($errMsg, 422);

        # [3]
        $sale = Sale::find($id);

        if (!$sale) return $this->error('The sale invoice was not found', 404);

        # [4]
        $oldDetails = &$sale->details;

        # [5]
        foreach ($newDetails as &$detail) {
            $detail['sale_id'] = $sale->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        $oldProductsIds = Arr::pluck($oldDetails, 'product_id');

        $newProductsIds = Arr::pluck($newDetails, 'product_id');

        $ids = [...$oldProductsIds, ...$newProductsIds];

        # [6]
        $products = Product::find($ids);

        # [7]
        $allDetails = [...$newDetails, ...$oldDetails];

        # [8]
        list($isValid, $errMsg) = $this->checkProductsWithVariants($allDetails, $products);

        if (!$isValid) return $this->error($errMsg, 422);

        $oldIsReceived = $sale->status == Constants::INVOICE_RECEIVED;

        $newIsReceived = $req->status == Constants::INVOICE_RECEIVED;

        # [9]
        $detailsHasVariants = $this->filterDetailsVariants($allDetails, true);

        $variants = [];

        # [10]
        if (($newIsReceived || $oldIsReceived) && count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        # [11] Sum Old Quantity
        if ($oldIsReceived) {
            $this->sumQuantity($variants, $oldDetails, $products);
        }

        if ($newIsReceived) {

            # [12] Check Quantity before subtract
            list($isValid, $errMsg) = $this->checkingQuantity($newDetails, $products, $variants);

            if (!$isValid) return $this->error($errMsg, 422);

            # [13] Subtract New Quantity
            $this->subtractQuantity($variants, $newDetails, $products);
        }

        if ($oldIsReceived) {
            $this->sumWarehouseQuantity($req->warehouse_id, $oldDetails, $products, 'sale_unit');
        }
        
        if ($newIsReceived) {
            $this->subtractWarehouseQuantity($req->warehouse_id, $newDetails, $products, 'sale_unit');
        }

        # [14]
        SaleDetail::where('sale_id', $sale->id)->delete();

        # [15]
        if ($oldIsReceived || $newIsReceived) {
            $this->updateInstock($products, $variants);
        }

        $sale->fill($attr);

        # [16]
        $sale->save();

        # [17]
        SaleDetail::insert($newDetails);

        return $this->success([], "The sale invoice has been updated successfully");
    }


    public function moveToTrash(Request $req, $id)
    {
        SaleRequest::validationId($req);

        $sale = Sale::find($id);

        if (!$sale) return $this->error('This sale is not found', 404);

        if ($sale->status === Constants::INVOICE_RECEIVED) {
            return $this->error('Sorry, you can\'t remove this sale invoice because it received but you can create returned sale invoice', 422);
        }

        $sale->delete();

        return $this->success('The sale invoice has been moved to trash successfully');
    }


    public function trashed()
    {
        $sales = Sale::onlyTrashed()->get();

        return $this->success($sales);
    }


    public function restore(Request $req, $id)
    {
        SaleRequest::validationId($req);

        $isDone = Sale::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The sale invoice is not in the trash', 404);

        return $this->success($id, 'The sale invoice has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        SaleRequest::validationId($req);

        $isDone = Sale::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The sale invoice is not in the trash', 404);

        return $this->success($id, 'The sale invoice has been deleted successfully');
    }
}
