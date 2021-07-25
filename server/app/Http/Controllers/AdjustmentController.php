<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Helpers\Constants;
use App\Models\Adjustment;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Helpers\CustomException;
use App\Models\AdjustmentDetail;
use App\Models\ProductWarehouse;
use App\Traits\InvoiceOperations;
use Illuminate\Support\Facades\DB;
use App\Requests\AdjustmentRequest;

class AdjustmentController extends Controller
{
    use InvoiceOperations;

    public function index()
    {
        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $adjustments = Adjustment::with($warehouse)->get();

        $adjustments = $adjustments->map(function ($adjustment) {
            return [
                'id'            => $adjustment->id,
                'reference'     => $adjustment->reference,
                'items_count'   => $adjustment->items_count,
                'warehouse'     => $adjustment->warehouse,
                'date'          => $adjustment->date
            ];
        });

        return $this->success($adjustments);
    }


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {

                $attr = AdjustmentRequest::validationCreate($req);

                $details = &$attr['products'];

                $this->check_distinct($details);

                if ($req->status == Constants::ADJUSTMENT_APPROVED) {
                    
                    $products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $details);
    
                    $ids = Arr::pluck($details, 'product_id');
    
                    $products = Product::select(['id', 'name', 'has_variants'])->find($ids);
    
                    $this->check_products_with_variants($details, $products);
    
                    $this->checking_relations($details, $products_warehouse, $products);
    
                    $this->set_instock_by_operation($products_warehouse, $details);
    
                    $this->update_instock($products_warehouse);
                }

                $adjustment = Adjustment::create($attr);

                foreach ($details as &$detail) {
                    $detail['adjustment_id'] = $adjustment->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                AdjustmentDetail::insert($details);
            }, 10);

            return $this->success([], "The Adjustment has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function update(Request $req, $id)
    {
        try {
            DB::transaction(function () use ($req, $id) {

                $attr = AdjustmentRequest::validationCreate($req);

                $new_details = &$attr['products'];

                $this->check_distinct($new_details);

                $adjustment = Adjustment::find($id);

                if (!$adjustment) throw CustomException::withMessage('id', 'The adjustment was not found', 404);

                $old_details = &$adjustment->details;

                foreach ($new_details as &$detail) {
                    $detail['adjustment_id'] = $adjustment->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                $old_products_ids = Arr::pluck($old_details, 'product_id');

                $new_products_ids = Arr::pluck($new_details, 'product_id');

                $ids = [...$old_products_ids, ...$new_products_ids];

                $products = Product::select(['id', 'name', 'has_variants'])->find($ids);

                $all_details = [...$new_details, ...$old_details];

                $this->check_products_with_variants($all_details, $products);

                $old_is_approved = $adjustment->status == Constants::ADJUSTMENT_APPROVED;

                $new_is_approved = $req->status == Constants::ADJUSTMENT_APPROVED;

                # Restore Old Instock
                if ($old_is_approved) {

                    $old_products_warehouse = $this->get_products_warehouse_by_details($adjustment->warehouse_id, $old_details);
    
                    $this->checking_relations($old_details, $old_products_warehouse, $products);
    
                    $this->set_instock_by_operation($old_products_warehouse, $old_details, true);
    
                    $this->update_instock($old_products_warehouse);
                }

                # Set New Instock
                if ($new_is_approved) {

                    $new_products_warehouse = $this->get_products_warehouse_by_details($adjustment->warehouse_id, $new_details);
    
                    $this->checking_relations($new_details, $new_products_warehouse, $products);
    
                    $this->set_instock_by_operation($new_products_warehouse, $new_details);
    
                    $this->update_instock($new_products_warehouse);
                }

                AdjustmentDetail::where('adjustment_id', $adjustment->id)->delete();

                $adjustment->fill($attr);

                $adjustment->save();

                AdjustmentDetail::insert($new_details);
            }, 10);

            return $this->success([], "The Adjustment has been updated successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function moveToTrash(Request $req, $id)
    {
        AdjustmentRequest::validationId($req);

        $adjustment = Adjustment::find($id);

        if (!$adjustment) return $this->error('This adjustment is not found', 404);

        $adjustment->delete();

        return $this->success('The adjustment has been moved to trash successfully');
    }


    public function trashed()
    {
        $adjustments = Adjustment::onlyTrashed()->get();

        return $this->success($adjustments);
    }


    public function restore(Request $req, $id)
    {
        AdjustmentRequest::validationId($req);

        $isDone = Adjustment::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The adjustment is not in the trash', 404);

        return $this->success($id, 'The adjustment has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        AdjustmentRequest::validationId($req);

        $isDone = Adjustment::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The adjustment is not in the trash', 404);

        AdjustmentDetail::where('adjustment_id', $id)->delete();

        return $this->success($id, 'The adjustment has been deleted successfully');
    }


    private function set_instock_by_operation(&$products_warehouse, $details, $reverse = false)
    {
        foreach ($details as $key => $detail) {

            $product_warehouse = $this->getProductWarehouseByDetail($products_warehouse, $detail);

            $num = $key + 1;

            if (!$product_warehouse) {
                throw CustomException::withMessage("product.{$num}", "product.{$num} doesn't have this variant");
            }

            if ($detail['type'] == Constants::ADJUSTMENT_DETAILS_SUBTRACTION) {

                if ($reverse) {
                    $product_warehouse->instock += $detail['quantity'];
                } else {
                    $product_warehouse->instock -= $detail['quantity'];
                }
            } else {

                if ($reverse) {
                    $product_warehouse->instock -= $detail['quantity'];
                } else {
                    $product_warehouse->instock += $detail['quantity'];
                }
            }

            if ($product_warehouse->instock < 0) {
                throw CustomException::withMessage("product.{$num}", "product.{$num} has {$product_warehouse->instock} instock and you tried to subtract {$detail['quantity']}");
            }
        }
    }
}
