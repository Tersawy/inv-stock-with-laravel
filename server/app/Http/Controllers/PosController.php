<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Helpers\Constants;
use App\Models\SaleDetail;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Requests\SaleRequest;
use App\Helpers\CustomException;
use App\Traits\InvoiceOperations;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    use InvoiceOperations;

    public $unitName = 'sale_unit';


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {

                $attr = SaleRequest::validationCreate($req);

                $attr['status'] = Constants::SALE_COMPLETED;
                $attr['is_pos'] = 1;

                $details = &$attr['products'];

                $this->check_distinct($details);

                $products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $details);

                $ids = Arr::pluck($details, 'product_id');

                $products = Product::with($this->unitName)->select(['id', 'name', 'has_variants', 'sale_unit_id'])->find($ids);

                $this->check_products_with_variants($details, $products);

                $this->checking_relations($details, $products_warehouse, $products);

                $sale = Sale::create($attr);

                foreach ($details as &$detail) {
                    $detail['sale_id'] = $sale->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                SaleDetail::insert($details);

                $this->checking_quantity($details, $products_warehouse, $products);

                $this->subtract_instock($details, $products_warehouse, $products);

                $this->update_instock($products_warehouse);
            }, 10);

            return $this->success([], "The sale invoice has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }
}
