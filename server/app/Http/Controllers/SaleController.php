<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Helpers\Constants;
use App\Models\SaleDetail;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Requests\SaleRequest;
use App\Models\ProductVariant;
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

            list($isValid, $errMsg) = $this->checkingQuantity($details, $products, $variants, false, []);

            if (!$isValid) return $this->error($errMsg, 422);

            $this->subtractQuantity($variants, $details, $products);

            $productsHasNoVariants = $this->filterProductsVariants($products, false);

            $this->updateMultiple($productsHasNoVariants, Product::class, 'instock');

            $this->updateMultiple($variants, ProductVariant::class, 'instock');
        }

        return $this->success([], "The Sale has been created successfully");
    }
}
