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

    public function index()
    {
        $from_warehouse = ['from_warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $to_warehouse = ['from_warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $with_fields = array_merge([], $from_warehouse, $to_warehouse);

        $transfers = Transfer::with($with_fields)->get();

        $transfers = $transfers->map(function ($transfer) {
            return [
                'id'                => $transfer->id,
                'reference'         => $transfer->reference,
                'supplier'          => $transfer->supplier,
                'warehouse'         => $transfer->warehouse,
                'status'            => $transfer->status,
                'grand_total'       => $transfer->grand_total,
                'paid'              => $transfer->paid,
                'due'               => $transfer->due,
                'payment_status'    => $transfer->payment_status,
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
}
