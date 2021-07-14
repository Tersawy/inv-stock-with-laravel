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

        $checkingQuantityParams = [$attr['products'], $products];

        if (count($detailsHasVariants)) {

            $variants = $this->getVariants($detailsHasVariants);

            $checkingQuantityParams = [...$checkingQuantityParams, $variants];

            list($isValid, $errMsg) = $this->checkingRelations($detailsHasVariants, $variants, $products);

            if (!$isValid) return $this->error($errMsg, 422);
        }

        list($isValid, $errMsg) = $this->checkingQuantity(...$checkingQuantityParams);

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

        return $this->success([], "The Purchase Return has been created successfully");
    }
}
