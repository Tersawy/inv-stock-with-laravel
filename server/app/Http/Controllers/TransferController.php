<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transfer;
use App\Helpers\Constants;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\TransferDetail;
use App\Helpers\CustomException;
use App\Requests\TransferRequest;
use App\Traits\InvoiceOperations;
use Illuminate\Support\Facades\DB;
use App\Traits\ProductWarehouseOperations;

class TransferController extends Controller
{
    use InvoiceOperations, ProductWarehouseOperations;

    public $unitName = 'purchase_unit';

    protected $filterationFields = [
        'reference'         => 'reference',
        'date'              => 'date',
        'from_warehouse'    => 'from_warehouse_id',
        'to_warehouse'      => 'to_warehouse_id',
        'status'            => 'status'
    ];

    protected $searchFields = ['date', 'reference'];

    public function index(Request $req)
    {
        $from_warehouse = ['from_warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $to_warehouse = ['from_warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $with_fields = array_merge($from_warehouse, $to_warehouse);

        $transfers = Transfer::query();

        $this->handleQuery($req, $transfers);

        $transfers = Transfer::select(['id', 'reference', 'status', 'from_warehouse_id', 'to_warehouse_id', 'items_count', 'total_price', 'date'])->with($with_fields)->paginate($req->per_page);

        $transfers->getCollection()->transform(function ($transfer) {
            return [
                'id'                => $transfer->id,
                'reference'         => $transfer->reference,
                'from_warehouse'    => $transfer->from_warehouse,
                'to_warehouse'      => $transfer->to_warehouse,
                'status'            => $transfer->status,
                'total_price'       => $transfer->total_price,
                'items_count'       => $transfer->items_count,
                'paid'              => $transfer->paid,
                'due'               => $transfer->due,
                'date'              => $transfer->date
            ];
        });

        return $this->success($transfers);
    }


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {

                $attr = TransferRequest::validationCreate($req);

                $details = &$attr['products'];

                $attr['items_count'] = count($details);

                $this->check_distinct($details);

                $from_products_warehouse = $this->get_products_warehouse_by_details($req->from_warehouse_id, $details);

                $ids = Arr::pluck($details, 'product_id');

                $products = Product::select(['id', 'name', 'has_variants', 'purchase_unit_id'])->find($ids);

                $this->check_products_with_variants($details, $products);

                $this->checking_relations($details, $from_products_warehouse, $products);

                $this->checking_quantity($details, $from_products_warehouse, $products);

                $transfer = Transfer::create($attr);

                $transfer->reference = 'TR_' . (1110 + $transfer->id);

                $transfer->save();

                foreach ($details as &$detail) {
                    $detail['transfer_id'] = $transfer->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                TransferDetail::insert($details);

                # Subtract Quantity From Old Warehouse
                if ($req->status == Constants::TRANSFER_COMPLETED || $req->status == Constants::TRANSFER_SENT) {

                    $this->subtract_instock($details, $from_products_warehouse, $products);

                    $this->update_instock($from_products_warehouse);
                }

                # Sum Quantity To New Warehouse
                if ($req->status == Constants::TRANSFER_COMPLETED) {

                    $to_products_warehouse = $this->get_products_warehouse_by_details($req->to_warehouse_id, $details);

                    $this->sum_instock($details, $to_products_warehouse, $products);

                    $this->update_instock($to_products_warehouse);
                }
            }, 10);

            return $this->success([], "The Transfer has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function update(Request $req, $id)
    {
        try {
            DB::transaction(function () use ($req, $id) {

                $attr = TransferRequest::validationUpdate($req);

                $new_details = &$attr['products'];

                $this->check_distinct($new_details);

                $transfer = Transfer::find($id);

                if (!$transfer) throw CustomException::withMessage('id', 'The transfer was not found', 404);

                $old_details = &$transfer->details;

                foreach ($new_details as &$detail) {
                    $detail['transfer_id'] = $transfer->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                $old_products_ids = Arr::pluck($old_details, 'product_id');

                $new_products_ids = Arr::pluck($new_details, 'product_id');

                $ids = [...$old_products_ids, ...$new_products_ids];

                $products = Product::select(['id', 'name', 'has_variants', 'purchase_unit_id'])->find($ids);

                $all_details = [...$new_details, ...$old_details];

                $this->check_products_with_variants($all_details, $products);

                $old_is_completed = $transfer->status == Constants::TRANSFER_COMPLETED;

                $old_is_sent = $transfer->status == Constants::TRANSFER_SENT;

                $new_is_completed = $req->status == Constants::TRANSFER_COMPLETED;

                $new_is_sent = $req->status == Constants::TRANSFER_SENT;

                # Sum Old Quantity To Old Warehouse Of Old Transfer
                if ($old_is_completed || $old_is_sent) {

                    $old_from_products_warehouse = $this->get_products_warehouse_by_details($transfer->from_warehouse_id, $old_details);

                    $this->checking_relations($old_details, $old_from_products_warehouse, $products);

                    $this->sum_instock($old_details, $old_from_products_warehouse, $products);

                    $this->update_instock($old_from_products_warehouse);
                }

                # Subtract Old Quantity From New Warehouse of Old Transfer
                if ($old_is_completed) {

                    $old_to_products_warehouse = $this->get_products_warehouse_by_details($transfer->to_warehouse_id, $old_details);

                    $this->checking_relations($old_details, $old_to_products_warehouse, $products);

                    $this->checking_quantity($old_details, $old_to_products_warehouse, $products);

                    $this->subtract_instock($old_details, $old_to_products_warehouse, $products);

                    $this->update_instock($old_to_products_warehouse);
                }

                # Subtract New Quantity From Old Warehouse Of New Transfer
                if ($new_is_completed || $new_is_sent) {

                    $new_from_products_warehouse = $this->get_products_warehouse_by_details($req->from_warehouse_id, $new_details);

                    $this->checking_relations($new_details, $new_from_products_warehouse, $products);

                    $this->checking_quantity($new_details, $new_from_products_warehouse, $products);

                    $this->subtract_instock($new_details, $new_from_products_warehouse, $products);

                    $this->update_instock($new_from_products_warehouse);
                }

                # Sum New Quantity To New Warehouse Of New Transfer
                if ($new_is_completed) {

                    $new_to_products_warehouse = $this->get_products_warehouse_by_details($req->to_warehouse_id, $new_details);

                    $this->checking_relations($new_details, $new_to_products_warehouse, $products);

                    $this->sum_instock($new_details, $new_to_products_warehouse, $products);

                    $this->update_instock($new_to_products_warehouse);
                }

                TransferDetail::where('transfer_id', $transfer->id)->delete();

                $transfer->fill($attr);

                $transfer->items_count = count($new_details);

                $transfer->save();

                TransferDetail::insert($new_details);
            }, 10);

            return $this->success([], "The transfer has been updated successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function moveToTrash(Request $req, $id)
    {
        TransferRequest::validationId($req);

        $transfer = Transfer::find($id);

        if (!$transfer) return $this->error('This transfer is not found', 404);

        if ($transfer->status == Constants::TRANSFER_COMPLETED) {
            return $this->error('Sorry, you can\'t remove this transfer because it completed', 422);
        }

        if ($transfer->status == Constants::TRANSFER_SENT) {
            return $this->error('Sorry, you can\'t remove this transfer because it sent', 422);
        }

        $transfer->delete();

        return $this->success('The transfer has been moved to trash successfully');
    }


    public function trashed()
    {
        $transfers = Transfer::onlyTrashed()->get();

        return $this->success($transfers);
    }


    public function restore(Request $req, $id)
    {
        TransferRequest::validationId($req);

        $isDone = Transfer::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The transfer is not in the trash', 404);

        return $this->success($id, 'The transfer has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        TransferRequest::validationId($req);

        $isDone = Transfer::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The transfer is not in the trash', 404);

        TransferDetail::where('transfer_id', $id)->delete();

        return $this->success($id, 'The transfer has been deleted successfully');
    }
}
