<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Helpers\Constants;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Helpers\CustomException;
use App\Models\ProductWarehouse;
use App\Traits\InvoiceOperations;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseReturnDetail;
use App\Requests\PurchaseReturnRequest;

class PurchaseReturnController extends Controller
{
    use InvoiceOperations;

    public $unitName = 'purchase_unit';

    protected $filterationFields = [
        'date'           => 'date',
        'reference'      => 'reference',
        'supplier'       => 'supplier_id',
        'warehouse'      => 'warehouse_id',
        'status'         => 'status',
        'payment_status' => 'payment_status'
    ];

    protected $searchFields = ['reference', 'date'];

    public function index(Request $req)
    {
        $supplier = ['supplier' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $with_fields = array_merge($supplier, $warehouse);

        $purchases = PurchaseReturn::query();

        $this->handleQuery($req, $purchases);

        $purchases = PurchaseReturn::select(['id', 'reference', 'status', 'payment_status', 'supplier_id', 'warehouse_id', 'total_price', 'date', 'paid'])->with($with_fields)->paginate($req->per_page);

        $purchases->getCollection()->transform(function ($purchase) {
            return [
                'id'                => $purchase->id,
                'reference'         => $purchase->reference,
                'supplier'          => $purchase->supplier,
                'warehouse'         => $purchase->warehouse,
                'status'            => $purchase->status,
                'total_price'       => $purchase->total_price,
                'paid'              => $purchase->paid,
                'due'               => $purchase->due,
                'payment_status'    => $purchase->payment_status,
                'date'              => $purchase->date
            ];
        });

        return $this->success($purchases);
    }


    public function show(Request $req)
    {
        PurchaseReturnRequest::validationId($req);

        $details = [
            'details' => function ($query) {
                $query->select(['purchase_id', 'product_id', 'variant_id', 'cost', 'tax', 'tax_method', 'discount', 'discount_method', 'quantity']);
            },
            'details.product' => function ($query) {
                $query->select(['id', 'name', 'code', 'purchase_unit_id']);
            },
            'details.product.purchase_unit' => function ($query) {
                $query->select(['id', 'short_name', 'operator', 'value']);
            },
            'details.variant' => function ($query) {
                $query->select(['id', 'name', 'product_id']);
            },
            'details.image' => function ($query) {
                $query->select(['product_id', 'name']);
            },
        ];

        $select = ['id', 'warehouse_id', 'supplier_id', 'discount', 'discount_method', 'tax', 'status', 'shipping', 'total_price', 'note', 'date'];

        $purchase = PurchaseReturn::select($select)->with($details)->find($req->id);

        if (!$purchase) return $this->error('The purchase invoice was not found', 404);

        $purchase->details->transform(function ($detail) use ($purchase) {

            $product_warehouse = ProductWarehouse::where('variant_id', $detail->variant_id)->where('warehouse_id', $purchase->warehouse_id)->where('product_id', $detail->product_id)->first();

            $instock = 0;

            if ($detail->product->purchase_unit->operator == "*") {
                $instock =  $product_warehouse->instock / $detail->product->purchase_unit->value;
            } else {
                $instock = $product_warehouse->instock * $detail->product->purchase_unit->value;
            }

            return [
                'id'                => $detail->product_id,
                'variant_id'        => $detail->variant_id,
                'variant'           => $detail->variant ? $detail->variant->name : null,
                'unit_cost'         => $detail->cost,
                'quantity'          => $detail->quantity,
                'instock'           => $instock,
                'tax'               => $detail->tax,
                'tax_method'        => $detail->tax_method,
                'discount'          => $detail->discount,
                'discount_method'   => $detail->discount_method,
                'code'              => $detail->product->code,
                'name'              => $detail->product->name,
                'purchase_unit'     => $detail->product->purchase_unit->short_name,
                'image'             => $detail->image->name,
            ];
        });

        $purchase->products = $purchase->details;

        unset($purchase->details);

        return $this->success($purchase);
    }


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {

                $attr = PurchaseReturnRequest::validationCreate($req);

                $details = &$attr['products'];

                $this->check_distinct($details);

                $products_warehouse = $this->get_products_warehouse_by_details($req->warehouse_id, $details);

                $ids = Arr::pluck($details, 'product_id');

                $products = Product::with($this->unitName)->select(['id', 'name', 'has_variants', 'purchase_unit_id'])->find($ids);

                $this->check_products_with_variants($details, $products);

                $this->checking_relations($details, $products_warehouse, $products);

                $this->checking_quantity($details, $products_warehouse, $products);

                $purchase = PurchaseReturn::create($attr);

                $purchase->reference = 'RT_' . (1110 + $purchase->id);

                $purchase->save();

                foreach ($details as &$detail) {
                    $detail['purchase_return_id'] = $purchase->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                PurchaseReturnDetail::insert($details);

                if ($req->status === Constants::PURCHASE_RETURN_COMPLETED) {

                    $this->subtract_instock($details, $products_warehouse, $products);

                    $this->update_instock($products_warehouse);
                }
            }, 10);

            return $this->success([], "The Purchase return invoice has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function update(Request $req, $id)
    {
        try {
            DB::transaction(function () use ($req, $id) {

                $attr = PurchaseReturnRequest::validationUpdate($req);

                $new_details = &$attr['products'];

                $this->check_distinct($new_details);

                $purchase = PurchaseReturn::find($id);

                if (!$purchase) throw CustomException::withMessage('id', 'The purchase return invoice was not found', 404);

                $old_details = &$purchase->details;

                foreach ($new_details as &$detail) {
                    $detail['purchase_return_id'] = $purchase->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                $oldProductsIds = Arr::pluck($old_details, 'product_id');

                $newProductsIds = Arr::pluck($new_details, 'product_id');

                $ids = [...$oldProductsIds, ...$newProductsIds];

                $products = Product::with($this->unitName)->select(['id', 'name', 'has_variants', 'purchase_unit_id'])->find($ids);

                $all_details = [...$new_details, ...$old_details];

                $this->check_products_with_variants($all_details, $products);

                $old_is_completed = $purchase->status == Constants::PURCHASE_RETURN_COMPLETED;

                $new_is_completed = $req->status == Constants::PURCHASE_RETURN_COMPLETED;

                # Sum Old Quantity
                if ($old_is_completed) {

                    $old_products_warehouse = $this->get_products_warehouse_by_details($purchase->warehouse_id, $old_details);

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

                PurchaseReturnDetail::where('purchase_return_id', $purchase->id)->delete();

                $purchase->fill($attr);

                $purchase->save();

                PurchaseReturnDetail::insert($new_details);
            }, 10);

            return $this->success([], "The Purchase return invoice has been updated successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function moveToTrash(Request $req, $id)
    {
        PurchaseReturnRequest::validationId($req);

        $purchase = PurchaseReturn::find($id);

        if (!$purchase) return $this->error('This purchase return invoice is not found', 404);

        if ($purchase->status === Constants::PURCHASE_RETURN_COMPLETED) {
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

        PurchaseReturnDetail::where('purchase_return_id', $id)->delete();

        return $this->success($id, 'The purchase return invoice has been deleted successfully');
    }
}
