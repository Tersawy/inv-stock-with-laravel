<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\PurchaseDetail;
use App\Requests\PurchaseRequest;
use App\Traits\InvoiceOperations;

class PurchaseController extends Controller
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

        $purchases = Purchase::with($withFields)->get();

        $purchases = $purchases->map(function ($purchase) {
            return [
                'id'                => $purchase->id,
                'reference'         => 'PR_' . (1110 + $purchase->id),
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
        $attr = $req->validate(PurchaseRequest::ruleOfCreate());

        list($isValid, $errMsg) = $this->checkDistinct($attr['products']);

        if (!$isValid) return $this->error($errMsg, 422);

        $ids = Arr::pluck($attr['products'], 'product_id');

        $products = Product::find($ids);

        list($isValid, $errMsg) = $this->checkProductsWithVariants($attr['products'], $products);

        if (!$isValid) return $this->error($errMsg, 422);

        $detailsHasVariants = $this->filterDetailsVariants($attr['products'], true);

        if (count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        $purchase = Purchase::create($attr);

        foreach ($attr['products'] as &$detail) {
            $detail['purchase_id'] = $purchase->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        PurchaseDetail::insert($attr['products']);

        if ($req->status === Purchase::RECEIVED) {

            if (count($detailsHasVariants)) {

                $this->sumVariantsQuantity($variants, $detailsHasVariants);

                $this->updateMultiple($variants, ProductVariant::class, 'instock');
            }

            $productsHasNoVariants = $this->filterProductsVariants($products, false);

            if (count($productsHasNoVariants)) {

                $detailsHasNoVariants = $this->filterDetailsVariants($attr['products'], false);

                $this->sumProductsQuantity($productsHasNoVariants, $detailsHasNoVariants);

                $this->updateMultiple($productsHasNoVariants, Product::class, 'instock');
            }
        }

        return $this->success([], "The Purchase has been created successfully");
    }

    /**
        # 1 - Make validation for request update rule
        # 2 - Make Distinct validation for new details
        # 3 - Get Purchase with details
        # 4 - Split new and old details in two separate variables
        # 5 - Set purchase id into every new detail
        # 6 - Get products IDs from details for every type of detail separatly => oldProductsIds, newProductsId
        # 7 - Merge all ids to get all Products
        # 8 - Get all Products
        # 9 - Merge all details to check variants with products
        # 10 - Check Variants with Products => eg. if product has a variant and detail doesn't has a variant,
            the purchase was created a long time ago with products doesn't have variants then
            we updated product by added variant to it then we want to update this purchase with this product out variant !
            whats happen without this check ?!!
            it will be add quantity into product.instock but product has variant supposed to added quantity into variant.instock
        # 11 - check if new or old status is received to get variants and details to check relations between products with them variants
        # 12 - Subtract quantity from old products has not variants and variants by old details
            A) check if old status is received
                1- filter old details has variants and old details not has variants from old details
                2- if count of old details that has variants > 0
                    I) subtract quantity from old variants
                3- if count of old details that hasn't variants > 0
                    I) filter products has no variants
                   II) subtract quantity from products has no variants
        # 13 - Sum quantity to new products has not variants and variants by new details
            A) check if new status is received
                1- filter new details has variants and new details not has variants from new details
                2- if count of new details that has variants > 0
                    I) sum quantity to new variants
                3- if count of new details that hasn't variants > 0
                    I) sum quantity to new product
        # 14 - delete old details from `purchase_details` by $purchase->id
        # 15 - filter products has variants
        # 16 - update multiple products has variants
        # 17 - update multiple variants
        # 18 - update purchase with new data
        # 19 - insert new details
     */
    public function update(Request $req, $id)
    {
        $req->merge(['id' => $id]);

        # [1]
        $attr = $req->validate(PurchaseRequest::ruleOfUpdate());

        # [4]
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

        # [6]
        $oldProductsIds = Arr::pluck($oldDetails, 'product_id');

        # [6]
        $newProductsIds = Arr::pluck($newDetails, 'product_id');

        # [7]
        $ids = [...$oldProductsIds, ...$newProductsIds];

        # [8]
        $products = Product::find($ids);

        # [9]
        $allDetails = [...$newDetails, ...$oldDetails];

        # [10]
        list($isValid, $errMsg) = $this->checkProductsWithVariants($allDetails, $products);

        if (!$isValid) return $this->error($errMsg, 422);

        # [11]
        if ($purchase->status == Purchase::RECEIVED || $req->status == Purchase::RECEIVED) {

            $allDetailsHasVariants = $this->filterDetailsVariants($allDetails, true);

            if (count($allDetailsHasVariants)) {

                $variants = $this->getVariants($allDetailsHasVariants);

                list($isValid, $errMsg) = $this->checkingRelations($allDetailsHasVariants, $variants, $products);

                if (!$isValid) return $this->error($errMsg, 422);
            }
        }

        # [12] Subtract Old Quantity
        # [12] => A)
        if ($purchase->status == Purchase::RECEIVED) {

            # [12] => A) => 1
            $oldDetailsHasVariants = $this->filterDetailsVariants($oldDetails, true);
            # [12] => A) => 1
            $oldDetailsHasNoVariants = $this->filterDetailsVariants($oldDetails, false);

            # [12] => A) => 2
            if (count($oldDetailsHasVariants)) {
                # [12] => A) => 2 => I)
                $this->subtractVariantsQuantity($variants, $oldDetailsHasVariants);
            }

            # [12] => A) => 3
            if (count($oldDetailsHasNoVariants)) {
                # [12] => A) => 3 => I)
                $productsHasNoVariants = $this->filterProductsVariants($products, false);
                # [12] => A) => 3 => II)
                $this->subtractProductsQuantity($productsHasNoVariants, $oldDetailsHasNoVariants);
            }
        }

        # [13] Sum New Quantity
        # [13] => A)
        if ($req->status == Purchase::RECEIVED) {

            # [13] => A) => 1
            $newDetailsHasVariants = $this->filterDetailsVariants($newDetails, true);
            # [13] => A) => 1
            $newDetailsHasNoVariants = $this->filterDetailsVariants($newDetails, false);

            # [13] => A) => 2
            if (count($oldDetailsHasVariants)) {
                # [13] => A) => 2 => I)
                $this->sumVariantsQuantity($variants, $newDetailsHasVariants);
            }

            # [13] => A) => 3
            if (count($newDetailsHasNoVariants)) {
                # [13] => A) => 3 => I)
                $productsHasNoVariants = $this->filterProductsVariants($products, false);
                # [13] => A) => 3 => II)
                $this->sumProductsQuantity($productsHasNoVariants, $newDetailsHasNoVariants);
            }
        }

        # [14]
        PurchaseDetail::where('purchase_id', $purchase->id)->delete();

        # [15]
        $productsHasNoVariants = $this->filterProductsVariants($products, false);

        # [16]
        if (count($productsHasNoVariants)) {
            $this->updateMultiple($productsHasNoVariants, Product::class, 'instock');
        }

        # [17]
        if (count($variants)) {
            $this->updateMultiple($variants, ProductVariant::class, 'instock');
        }

        # [18]
        $purchase->fill($attr);

        # [18]
        $purchase->save();

        # [19]
        PurchaseDetail::insert($newDetails);

        return $this->success([], "The Purchase has been updated successfully");
    }
}
