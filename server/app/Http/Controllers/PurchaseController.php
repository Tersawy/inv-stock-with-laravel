<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use App\Requests\PurchaseRequest;
use App\Traits\InvoiceOperations;
use App\Traits\ProductWarehouseOperations;

class PurchaseController extends Controller
{
    use InvoiceOperations, ProductWarehouseOperations;


    public function index()
    {
        $supplier = ['supplier' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $withFields = array_merge([], $supplier, $warehouse);

        $purchases = Purchase::with($withFields)->get();

        $purchases = $purchases->map(function ($purchase) {
            return [
                'id'                => $purchase->id,
                'reference'         => $purchase->reference,
                'supplier'          => $purchase->supplier,
                'warehouse'         => $purchase->warehouse,
                'status'            => $purchase->status,
                'grand_total'       => $purchase->grand_total,
                'paid'              => $purchase->paid,
                'due'               => $purchase->due,
                'payment_status'    => $purchase->payment_status,
            ];
        });

        return $this->success($purchases);
    }


    public function create(Request $req)
    {
        $attr = PurchaseRequest::validationCreate($req);

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

        $purchase = Purchase::create($attr);

        foreach ($details as &$detail) {
            $detail['purchase_id'] = $purchase->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        PurchaseDetail::insert($details);

        if ($req->status === Constants::INVOICE_RECEIVED) {

            $this->sumQuantity($variants, $details, $products);

            $this->updateInstock($products, $variants);

            $this->sumWarehouseQuantity($req->warehouse_id, $details, $products, 'purchase_unit');
        }

        return $this->success([], "The Purchase has been created successfully");
    }

    /**
        # 1 - Make validation for request update rule
        # 2 - Make Distinct validation for new details
        # 3 - Get Purchase with details
        # 4 - Get old details
        # 5 - Set purchase id into every new detail
        # 6 - Get all Products by ids
        # 7 - Merge all details to check variants with products
        # 8 - Check Variants with Products => eg. if product has a variant and detail doesn't has a variant,
            the purchase was created a long time ago with products doesn't have variants then
            we updated product by added variant to it then we want to update this purchase with this product out variant !
            whats happen without this check ?!!
            it will be add quantity into product.instock but product has variant supposed to added quantity into variant.instock
        # 9  - Filter details to get whose has variant
        # 10 - check if new or old status is received and count of detailsHasVariants > 1 to get variants and details to check relations between products with them variants
        # 11 - check quantity if the old status is received before taking any action on the quantity to prevent it from begin set instock with a negative number,
            to show what is the broplem happen create purchase with any product with quantity 5 and status is received then go to create purchase return with the same product
            with quantity 5 and status completed if you check instock of this products will be 0 because be purchased and returned it, so if you update purchase with any status
            exept received whats happen ?! the product instock now is -5 !!!
        # 12 - Subtract quantity from old products has not variants and variants by old details
        # 13 - Sum quantity to new products has not variants and variants by new details
        # 14 - delete old details from `purchase_details` by $purchase->id
        # 15 - if old or new status is received update multiple products and variants
        # 16 - update purchase with new data
        # 17 - insert new details
     */
    public function update(Request $req, $id)
    {
        # [1]
        $attr = PurchaseRequest::validationUpdate($req);

        $newDetails = &$attr['products'];

        # [2]
        list($isValid, $errMsg) = $this->checkDistinct($newDetails);

        if (!$isValid) return $this->error($errMsg, 422);

        # [3]
        $purchase = Purchase::find($id);

        if (!$purchase) return $this->error('The purchase was not found', 404);

        # [4]
        $oldDetails = &$purchase->details;

        # [5]
        foreach ($newDetails as &$detail) {
            $detail['purchase_id'] = $purchase->id;
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

        $oldIsReceived = $purchase->status == Constants::INVOICE_RECEIVED;

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

        if ($oldIsReceived) {

            # [11]
            list($isValid, $errMsg) = $this->checkingQuantity($oldDetails, $products, $variants);

            if (!$isValid) return $this->error($errMsg, 422);

            # [12] Subtract Old Quantity
            $this->subtractQuantity($variants, $oldDetails, $products);

            $this->subtractWarehouseQuantity($req->warehouse_id, $oldDetails, $products, 'purchase_unit');
        }

        # [13] Sum New Quantity
        if ($newIsReceived) {
            $this->sumQuantity($variants, $newDetails, $products);

            $this->sumWarehouseQuantity($req->warehouse_id, $newDetails, $products, 'purchase_unit');
        }

        # [14]
        PurchaseDetail::where('purchase_id', $purchase->id)->delete();

        # [15]
        if ($oldIsReceived || $newIsReceived) {
            $this->updateInstock($products, $variants);
        }

        $purchase->fill($attr);

        # [16]
        $purchase->save();

        # [17]
        PurchaseDetail::insert($newDetails);

        return $this->success([], "The Purchase has been updated successfully");
    }


    public function moveToTrash(Request $req, $id)
    {
        PurchaseRequest::validationId($req);

        $purchase = Purchase::find($id);

        if (!$purchase) return $this->error('This purchase is not found', 404);

        if ($purchase->status === Constants::INVOICE_RECEIVED) {
            return $this->error('Sorry, you can\'t remove this purchase because it received but you can create returned purchase invoice', 422);
        }

        $purchase->delete();

        return $this->success('The purchase has been moved to trash successfully');
    }


    public function trashed()
    {
        $purchases = Purchase::onlyTrashed()->get();

        return $this->success($purchases);
    }


    public function restore(Request $req, $id)
    {
        PurchaseRequest::validationId($req);

        $isDone = Purchase::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The purchase invoice is not in the trash', 404);

        return $this->success($id, 'The purchase invoice has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        PurchaseRequest::validationId($req);

        $isDone = Purchase::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The purchase invoice is not in the trash', 404);

        return $this->success($id, 'The purchase invoice has been deleted successfully');
    }
}
