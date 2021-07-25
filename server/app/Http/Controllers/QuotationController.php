<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Helpers\CustomException;
use App\Traits\InvoiceOperations;
use App\Requests\QuotationRequest;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
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

        $with_fields = array_merge([], $customer, $warehouse);

        $quotations = Quotation::with($with_fields)->get();

        $quotations = $quotations->map(function ($quotation) {
            return [
                'id'            => $quotation->id,
                'reference'     => $quotation->reference,
                'customer'      => $quotation->customer,
                'warehouse'     => $quotation->warehouse,
                'status'        => $quotation->status,
                'grand_total'   => $quotation->grand_total,
                'date'          => $quotation->date
            ];
        });

        return $this->success($quotations);
    }


    public function create(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {
                $attr = QuotationRequest::validationCreate($req);

                $details = &$attr['products'];

                $this->check_distinct($details);

                $quotation = Quotation::create($attr);

                foreach ($details as &$detail) {
                    $detail['quotation_id'] = $quotation->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                QuotationDetail::insert($details);
            }, 10);

            return $this->success([], "The quotation has been created successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function update(Request $req, $id)
    {
        try {
            DB::transaction(function () use ($req, $id) {
                $attr = QuotationRequest::validationUpdate($req);

                $new_details = &$attr['products'];

                $this->check_distinct($new_details);

                $quotation = Quotation::find($id);

                if (!$quotation) throw CustomException::withMessage('id', 'The quotation was not found', 404);

                foreach ($new_details as &$detail) {
                    $detail['quotation_id'] = $quotation->id;
                    $detail['variant_id'] = Arr::get($detail, 'variant_id');
                }

                QuotationDetail::where('quotation_id', $quotation->id)->delete();

                $quotation->fill($attr);

                $quotation->save();

                QuotationDetail::insert($new_details);
            }, 10);

            return $this->success([], "The quotation has been updated successfully");
        } catch (CustomException $e) {
            return $this->error($e->first_error(), $e->status_code());
        }
    }


    public function moveToTrash(Request $req, $id)
    {
        QuotationRequest::validationId($req);

        $quotation = Quotation::find($id);

        if (!$quotation) return $this->error('This quotation is not found', 404);

        $quotation->delete();

        return $this->success('The quotation has been moved to trash successfully');
    }


    public function trashed()
    {
        $quotations = Quotation::onlyTrashed()->get();

        return $this->success($quotations);
    }


    public function restore(Request $req, $id)
    {
        QuotationRequest::validationId($req);

        $isDone = Quotation::onlyTrashed()->where('id', $id)->restore();

        if (!$isDone) return $this->error('The quotation is not in the trash', 404);

        return $this->success($id, 'The quotation has been restored successfully');
    }


    public function remove(Request $req, $id)
    {
        QuotationRequest::validationId($req);

        $isDone = Quotation::onlyTrashed()->where('id', $id)->forceDelete();

        if (!$isDone) return $this->error('The quotation is not in the trash', 404);

        QuotationDetail::where('quotation_id', $id)->delete();

        return $this->success($id, 'The quotation has been deleted successfully');
    }
}
