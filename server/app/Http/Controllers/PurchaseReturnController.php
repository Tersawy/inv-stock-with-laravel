<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Requests\PurchaseReturnRequest;
use App\Traits\InvoiceOperations;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;

class PurchaseReturnController extends Controller
{
    use InvoiceOperations;


    public function index()
    {
        $supplier = ['supplier' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $withFields = array_merge([], $supplier, $warehouse);

        $purchases = PurchaseReturn::with($withFields)->get();

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
        $attr = PurchaseReturnRequest::validationCreate($req);

        list($isValid, $errMsg) = $this->checkDistinct($attr['products']);

        if (!$isValid) return $this->error($errMsg, 422);

        $ids = Arr::pluck($attr['products'], 'product_id');

        $products = Product::find($ids);

        list($isValid, $errMsg) = $this->checkProductsWithVariants($attr['products'], $products);

        if (!$isValid) return $this->error($errMsg, 422);

        $detailsHasVariants = $this->filterDetailsVariants($attr['products'], true);

        $variants = [];

        if (count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        list($isValid, $errMsg) = $this->checkingQuantity($attr['products'], $products, $variants, false, []);

        if (!$isValid) return $this->error($errMsg, 422);

        $purchase = PurchaseReturn::create($attr);

        foreach ($attr['products'] as &$detail) {
            $detail['purchase_return_id'] = $purchase->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        PurchaseReturnDetail::insert($attr['products']);

        if ($req->status === PurchaseReturn::COMPLETED) {

            if (count($detailsHasVariants)) {

                $this->subtractVariantsQuantity($variants, $detailsHasVariants);

                $this->updateMultiple($variants, ProductVariant::class, 'instock');
            }

            $productsHasNoVariants = $this->filterProductsVariants($products, false);

            if (count($productsHasNoVariants)) {

                $detailsHasNoVariants = $this->filterDetailsVariants($attr['products'], false);

                $this->subtractProductsQuantity($productsHasNoVariants, $detailsHasNoVariants);

                $this->updateMultiple($productsHasNoVariants, Product::class, 'instock');
            }
        }

        return $this->success([], "The Purchase return invoice has been created successfully");
    }


    /**
        # 1 - Make validation for request update rule
        # 2 - Make Distinct validation for new details
        # 3 - Get Purchase Return with details
        # 4 - Get old details
        # 5 - Set purchase return id into every new detail
        # 6 - Get all Products ny ids
        # 7 - Merge all details to check variants with products
        # 8 - Check Variants with Products => eg. if product has a variant and detail doesn't has a variant,
            the purchase return was created a long time ago with products doesn't have variants then
            we updated product by added variant to it then we want to update this purchase return with this product out variant !
            whats happen without this check ?!!
            it will be add quantity into product.instock but product has variant supposed to added quantity into variant.instock
        # 9  - Filter details to get whose has variant
        # 10 - check if new or old status is completed and count of detailsHasVariants > 1 to get variants and details to check relations between products with them variants
        # 11 - check quantity if the new status is completed before taking any action on the quantity to prevent it from begin set instock with a negative number
        # 12 - Sum old details quantity to instock to restore old instock if old status is completed
        # 13 - Subtract new details quantity from instock if new status is completed
        # 14 - delete old details from `purchase_return_details` by $purchase_return->id
        # 15 - if old or new status is completed update multiple products and variants
        # 16 - update purchase return with new data
        # 17 - insert new details
     */
    public function update(Request $req, $id)
    {
        # [1]
        $attr = PurchaseReturnRequest::validationUpdate($req);

        $newDetails = &$attr['products'];

        # [2]
        list($isValid, $errMsg) = $this->checkDistinct($newDetails);

        if (!$isValid) return $this->error($errMsg, 422);

        # [3]
        $purchase = PurchaseReturn::find($id);

        if (!$purchase) return $this->error('The purchase return invoice was not found', 404);

        # [4]
        $oldDetails = &$purchase->details;

        # [5]
        foreach ($newDetails as &$detail) {
            $detail['purchase_return_id'] = $purchase->id;
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

        $oldIsCompleted = $purchase->status == PurchaseReturn::COMPLETED;

        $newIsCompleted = $req->status == PurchaseReturn::COMPLETED;

        # [9]
        $detailsHasVariants = $this->filterDetailsVariants($allDetails, true);

        $variants = [];

        # [10]
        if (($oldIsCompleted || $newIsCompleted) && count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        # [11] Check Quantity before subtract
        if ($newIsCompleted) {
            list($isValid, $errMsg) = $this->checkingQuantity($newDetails, $products, $variants, $oldIsCompleted, $oldDetails);
            if (!$isValid) return $this->error($errMsg, 422);
        }

        # [12] Sum Old Quantity
        if ($oldIsCompleted) {
            $this->sumQuantity($variants, $oldDetails, $products);
        }

        # [13] Subtract New Quantity
        if ($newIsCompleted) {
            $this->subtractQuantity($variants, $newDetails, $products);
        }

        # [14]
        PurchaseReturnDetail::where('purchase_return_id', $purchase->id)->delete();

        # [15]
        if ($oldIsCompleted || $newIsCompleted) {

            $productsHasNoVariants = $this->filterProductsVariants($products, false);

            $this->updateMultiple($productsHasNoVariants, Product::class, 'instock');

            $this->updateMultiple($variants, ProductVariant::class, 'instock');
        }

        $purchase->fill($attr);

        # [16]
        $purchase->save();

        # [17]
        PurchaseReturnDetail::insert($newDetails);

        return $this->success([], "The Purchase return invoice has been updated successfully");
    }


    public function moveToTrash(Request $req, $id)
    {
        PurchaseReturnRequest::validationId($req);

        $purchase = PurchaseReturn::find($id);

        if (!$purchase) return $this->error('This purchase return invoice is not found', 404);

        if ($purchase->status === PurchaseReturn::COMPLETED) {
            return $this->error('Sorry, you can\'t remove this purchase return invoice because it completed but you can create a new purchase invoice', 422);
        }

        $purchase->delete();

        return $this->success('The purchase return invoice has been moved to trash successfully');
    }


    public function trashed()
    {
        $purchases = PurchaseReturn::onlyTrashed()->get();

        return $this->success($purchases);
    }


    public function restore(Request $req, $id)
    {
        PurchaseReturnRequest::validationId($req);

        $isDone = PurchaseReturn::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The purchase return invoice is not in the trash', 404);

        return $this->success($id, 'The purchase return invoice has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        PurchaseReturnRequest::validationId($req);

        $isDone = PurchaseReturn::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The purchase return invoice is not in the trash', 404);

        return $this->success($id, 'The purchase return invoice has been deleted successfully');
    }
}