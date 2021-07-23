<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Requests\SaleReturnRequest;
use App\Traits\InvoiceOperations;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;

class SaleReturnController extends Controller
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

        $sales = SaleReturn::with($withFields)->get();

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
        $attr = SaleReturnRequest::validationCreate($req);

        $details = &$attr['products'];

        list($isValid, $errMsg) = $this->checkDistinct($details);

        if (!$isValid) return $this->error($errMsg, 422);

        $ids = Arr::pluck($details, 'product_id');

        $products = Product::find($ids);

        list($isValid, $errMsg) = $this->checkProductsWithVariants($details, $products);

        if (!$isValid) return $this->error($errMsg, 422);

        $detailsHasVariants = $this->filterDetailsVariants($details, true);

        $variants = [];

        if (count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        list($isValid, $errMsg) = $this->checkingQuantity($details, $products, $variants);

        if (!$isValid) return $this->error($errMsg, 422);

        $sale = SaleReturn::create($attr);

        foreach ($details as &$detail) {
            $detail['sale_return_id'] = $sale->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        SaleReturnDetail::insert($details);

        if ($req->status === Constants::SALE_RETURN_RECEIVED) {

            $this->sumQuantity($variants, $details, $products);

            $this->updateInstock($products, $variants);

            $this->sumWarehouseQuantity($req->warehouse_id, $details, $products, 'sale_unit');
        }

        return $this->success([], "The Sale return invoice has been created successfully");
    }


    /**
        # 1 - Make validation for request update rule
        # 2 - Make Distinct validation for new details
        # 3 - Get Sale Return with details
        # 4 - Get old details
        # 5 - Set sale return id into every new detail
        # 6 - Get all Products ny ids
        # 7 - Merge all details to check variants with products
        # 8 - Check Variants with Products => eg. if product has a variant and detail doesn't has a variant,
            the sale return was created a long time ago with products doesn't have variants then
            we updated product by added variant to it then we want to update this sale return with this product out variant !
            whats happen without this check ?!!
            it will be add quantity into product.instock but product has variant supposed to added quantity into variant.instock
        # 9  - Filter details to get whose has variant
        # 10 - check if new or old status is completed and count of detailsHasVariants > 1 to get variants and details to check relations between products with them variants
        # 11 - Sum old details quantity to instock to restore old instock if old status is completed
        # 12 - check quantity if the new status is completed before taking any action on the quantity to prevent it from begin set instock with a negative number
        # 13 - Subtract new details quantity from instock if new status is completed
        # 14 - delete old details from `sale_return_details` by $sale_return->id
        # 15 - if old or new status is completed update multiple products and variants
        # 16 - update sale return with new data
        # 17 - insert new details
     */
    public function update(Request $req, $id)
    {
        # [1]
        $attr = SaleReturnRequest::validationUpdate($req);

        $newDetails = &$attr['products'];

        # [2]
        list($isValid, $errMsg) = $this->checkDistinct($newDetails);

        if (!$isValid) return $this->error($errMsg, 422);

        # [3]
        $sale = SaleReturn::find($id);

        if (!$sale) return $this->error('The sale return invoice was not found', 404);

        # [4]
        $oldDetails = &$sale->details;

        # [5]
        foreach ($newDetails as &$detail) {
            $detail['sale_return_id'] = $sale->id;
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

        $oldIsCompleted = $sale->status == Constants::SALE_RETURN_RECEIVED;

        $newIsCompleted = $req->status == Constants::SALE_RETURN_RECEIVED;

        # [9]
        $detailsHasVariants = $this->filterDetailsVariants($allDetails, true);

        $variants = [];

        # [10]
        if (($oldIsCompleted || $newIsCompleted) && count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        if ($oldIsCompleted) {

            # [12] Check Quantity before subtract
            list($isValid, $errMsg) = $this->checkingQuantity($newDetails, $products, $variants);

            if (!$isValid) return $this->error($errMsg, 422);

            # [11] Subtract Old Quantity
            $this->subtractQuantity($variants, $oldDetails, $products);

            $this->subtractWarehouseQuantity($req->warehouse_id, $newDetails, $products, 'sale_unit');
        }

        # [13] Sum New Quantity
        if ($newIsCompleted) {
            $this->sumQuantity($variants, $newDetails, $products);

            $this->sumWarehouseQuantity($req->warehouse_id, $oldDetails, $products, 'sale_unit');
        }

        # [14]
        SaleReturnDetail::where('sale_return_id', $sale->id)->delete();

        # [15]
        if ($oldIsCompleted || $newIsCompleted) {
            $this->updateInstock($products, $variants);
        }

        $sale->fill($attr);

        # [16]
        $sale->save();

        # [17]
        SaleReturnDetail::insert($newDetails);

        return $this->success([], "The Sale return invoice has been updated successfully");
    }


    public function moveToTrash(Request $req, $id)
    {
        SaleReturnRequest::validationId($req);

        $sale = SaleReturn::find($id);

        if (!$sale) return $this->error('This sale return invoice is not found', 404);

        if ($sale->status === Constants::SALE_RETURN_RECEIVED) {
            return $this->error('Sorry, you can\'t remove this sale return invoice because it completed but you can create a new sale invoice', 422);
        }

        $sale->delete();

        return $this->success('The sale return invoice has been moved to trash successfully');
    }


    public function trashed()
    {
        $sales = SaleReturn::onlyTrashed()->get();

        return $this->success($sales);
    }


    public function restore(Request $req, $id)
    {
        SaleReturnRequest::validationId($req);

        $isDone = SaleReturn::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The sale return invoice is not in the trash', 404);

        return $this->success($id, 'The sale return invoice has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        SaleReturnRequest::validationId($req);

        $isDone = SaleReturn::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The sale return invoice is not in the trash', 404);

        return $this->success($id, 'The sale return invoice has been deleted successfully');
    }
}
