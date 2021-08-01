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

class SaleController extends Controller
{
    use InvoiceOperations;

    public $unitName = 'sale_unit';

    protected $filterationFields = [
        'date'           => 'date',
        'reference'      => 'reference',
        'customer'       => 'customer_id',
        'warehouse'      => 'warehouse_id',
        'status'         => 'status',
        'payment_status' => 'payment_status'
    ];

    protected $searchFields = ['reference', 'date'];

    public function index(Request $req)
    {
        $customer = ['customer' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $with_fields = array_merge($customer, $warehouse);

        $sales = Sale::query();

        $this->handleQuery($req, $sales);

        $sales = Sale::select(['id', 'reference', 'status', 'payment_status', 'customer_id', 'warehouse_id'])->with($with_fields)->paginate($req->per_page);

        $sales->getCollection()->transform(function ($sale) {
            return [
                'id'                => $sale->id,
                'reference'         => $sale->reference,
                'customer'          => $sale->customer,
                'warehouse'         => $sale->warehouse,
                'status'            => $sale->status,
                'grand_total'       => $sale->grand_total,
                'paid'              => $sale->paid,
                'due'               => $sale->due,
                'payment_status'    => $sale->payment_status
            ];
        });

        return $this->success($sales);
    }


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {

                $attr = SaleRequest::validationCreate($req);

                $details = &$attr['products'];

                $this->check_distinct($details);

                $products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $details);

                $ids = Arr::pluck($details, 'product_id');

                $products = Product::select(['id', 'name', 'has_variants', 'sale_unit_id'])->find($ids);

                $this->check_products_with_variants($details, $products);

                $this->checking_relations($details, $products_warehouse, $products);

                $sale = Sale::create($attr);

                $sale->reference = 'SL_' . (1110 + $sale->id);

                $sale->save();

                foreach ($details as &$detail) {
                    $detail['sale_id'] = $sale->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                SaleDetail::insert($details);

                if ($req->status === Constants::SALE_COMPLETED) {

                    $this->checking_quantity($details, $products_warehouse, $products);

                    $this->subtract_instock($details, $products_warehouse, $products);

                    $this->update_instock($products_warehouse);
                }
            }, 10);

            return $this->success([], "The sale invoice has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function update(Request $req, $id)
    {
        try {
            DB::transaction(function () use ($req, $id) {

                $attr = SaleRequest::validationUpdate($req);

                $new_details = &$attr['products'];

                $this->check_distinct($new_details);

                $sale = Sale::find($id);

                if (!$sale) throw CustomException::withMessage('id', 'The sale invoice was not found', 404);

                $old_details = &$sale->details;

                foreach ($new_details as &$detail) {
                    $detail['sale_id'] = $sale->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                $oldProductsIds = Arr::pluck($old_details, 'product_id');

                $newProductsIds = Arr::pluck($new_details, 'product_id');

                $ids = [...$oldProductsIds, ...$newProductsIds];

                $products = Product::select(['id', 'name', 'has_variants', 'sale_unit_id'])->find($ids);

                $all_details = [...$new_details, ...$old_details];

                $this->check_products_with_variants($all_details, $products);

                $old_is_completed = $sale->status == Constants::SALE_COMPLETED;

                $new_is_completed = $req->status == Constants::SALE_COMPLETED;

                # Sum Old Quantity
                if ($old_is_completed) {

                    $old_products_warehouse = $this->get_products_warehouse_by_details($sale->warehouse_id, $old_details);

                    $this->checking_relations($old_details, $old_products_warehouse, $products);

                    $this->sum_instock($old_details, $old_products_warehouse, $products);

                    $this->update_instock($old_products_warehouse);
                }

                # Subtract New Quantity
                if ($new_is_completed) {

                    $new_products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $new_details);

                    $this->checking_relations($new_details, $new_products_warehouse, $products);

                    $this->checking_quantity($new_details, $new_products_warehouse, $products);

                    $this->subtract_instock($new_details, $new_products_warehouse, $products);

                    $this->update_instock($new_products_warehouse);
                }

                SaleDetail::where('sale_id', $sale->id)->delete();

                $sale->fill($attr);

                $sale->save();

                SaleDetail::insert($new_details);
            }, 10);

            return $this->success([], "The sale invoice has been updated successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function moveToTrash(Request $req, $id)
    {
        SaleRequest::validationId($req);

        $sale = Sale::find($id);

        if (!$sale) return $this->error('This sale is not found', 404);

        if ($sale->status === Constants::SALE_COMPLETED) {
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

        SaleDetail::where('sale_id', $id)->delete();

        return $this->success($id, 'The sale invoice has been deleted successfully');
    }
}
