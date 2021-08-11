<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Helpers\Constants;
use App\Models\SaleReturn;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Helpers\CustomException;
use App\Models\SaleReturnDetail;
use App\Traits\InvoiceOperations;
use Illuminate\Support\Facades\DB;
use App\Requests\SaleReturnRequest;

class SaleReturnController extends Controller
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

        $sales = SaleReturn::query();

        $this->handleQuery($req, $sales);

        $sales = SaleReturn::select(['id', 'reference', 'status', 'payment_status', 'customer_id', 'warehouse_id', 'total_price', 'date'])->with($with_fields)->paginate($req->per_page);

        $sales->getCollection()->transform(function ($sale) {
            return [
                'id'                => $sale->id,
                'reference'         => $sale->reference,
                'customer'          => $sale->customer,
                'warehouse'         => $sale->warehouse,
                'status'            => $sale->status,
                'total_price'       => $sale->total_price,
                'paid'              => $sale->paid,
                'due'               => $sale->due,
                'payment_status'    => $sale->payment_status,
                'date'              => $sale->date
            ];
        });

        return $this->success($sales);
    }


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {

                $attr = SaleReturnRequest::validationCreate($req);

                $details = &$attr['products'];

                $this->check_distinct($details);

                $products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $details);

                $ids = Arr::pluck($details, 'product_id');

                $products = Product::select(['id', 'name', 'has_variants', 'sale_unit_id'])->find($ids);

                $this->check_products_with_variants($details, $products);

                $this->checking_relations($details, $products_warehouse, $products);

                $sale = SaleReturn::create($attr);

                $sale->reference = 'RT_' . (1110 + $sale->id);

                $sale->save();

                foreach ($details as &$detail) {
                    $detail['sale_return_id'] = $sale->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                SaleReturnDetail::insert($details);

                if ($req->status === Constants::SALE_RETURN_RECEIVED) {

                    $this->sum_instock($details, $products_warehouse, $products);

                    $this->update_instock($products_warehouse);
                }
            }, 10);

            return $this->success([], "The Sale return invoice has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function update(Request $req, $id)
    {
        try {
            DB::transaction(function () use ($req, $id) {

                $attr = SaleReturnRequest::validationUpdate($req);

                $new_details = &$attr['products'];

                $this->check_distinct($new_details);

                $sale = SaleReturn::find($id);

                if (!$sale) throw CustomException::withMessage('id', 'The sale return invoice was not found', 404);

                $old_details = &$sale->details;

                foreach ($new_details as &$detail) {
                    $detail['sale_return_id'] = $sale->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                $oldProductsIds = Arr::pluck($old_details, 'product_id');

                $newProductsIds = Arr::pluck($new_details, 'product_id');

                $ids = [...$oldProductsIds, ...$newProductsIds];

                $products = Product::select(['id', 'name', 'has_variants', 'sale_unit_id'])->find($ids);

                $all_details = [...$new_details, ...$old_details];

                $this->check_products_with_variants($all_details, $products);

                $old_is_received = $sale->status == Constants::SALE_RETURN_RECEIVED;

                $new_is_received = $req->status == Constants::SALE_RETURN_RECEIVED;

                # Subtract Old Quantity
                if ($old_is_received) {

                    $old_products_warehouse = $this->get_products_warehouse_by_details($sale->warehouse_id, $old_details);

                    $this->checking_relations($old_details, $old_products_warehouse, $products);

                    $this->checking_quantity($old_details, $old_products_warehouse, $products);

                    $this->subtract_instock($old_details, $old_products_warehouse, $products);

                    $this->update_instock($old_products_warehouse);
                }

                # Sum New Quantity
                if ($new_is_received) {

                    $new_products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $new_details);

                    $this->checking_relations($new_details, $new_products_warehouse, $products);

                    $this->checking_quantity($new_details, $new_products_warehouse, $products);

                    $this->sum_instock($new_details, $new_products_warehouse, $products);

                    $this->update_instock($new_products_warehouse);
                }

                SaleReturnDetail::where('sale_return_id', $sale->id)->delete();

                $sale->fill($attr);

                $sale->save();

                SaleReturnDetail::insert($new_details);
            }, 10);

            return $this->success([], "The Sale return invoice has been updated successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
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

        SaleReturnDetail::where('sale_return_id', $id)->delete();

        return $this->success($id, 'The sale return invoice has been deleted successfully');
    }
}
