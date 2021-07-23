<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Requests\QuotationRequest;

class QuotationController extends Controller
{
    public function index()
    {
        $customer = ['customer' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $withFields = array_merge([], $customer, $warehouse);

        $quotations = Quotation::with($withFields)->get();

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
        $attr = QuotationRequest::validationCreate($req);

        $details = &$attr['products'];

        list($isValid, $errMsg) = $this->checkDistinct($details);

        if (!$isValid) return $this->error($errMsg, 422);

        $quotation = Quotation::create($attr);

        foreach ($details as &$detail) {
            $detail['quotation_id'] = $quotation->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        QuotationDetail::insert($details);

        return $this->success([], "The quotation has been created successfully");
    }


    public function update(Request $req, $id)
    {
        $attr = QuotationRequest::validationUpdate($req);

        $newDetails = &$attr['products'];

        list($isValid, $errMsg) = $this->checkDistinct($newDetails);

        if (!$isValid) return $this->error($errMsg, 422);

        $quotation = Quotation::find($id);

        if (!$quotation) return $this->error('The quotation was not found', 404);

        foreach ($newDetails as &$detail) {
            $detail['quotation_id'] = $quotation->id;
            $detail['variant_id'] = Arr::get($detail, 'variant_id');
        }

        QuotationDetail::where('quotation_id', $quotation->id)->delete();

        $quotation->fill($attr);

        $quotation->save();

        QuotationDetail::insert($newDetails);

        return $this->success([], "The quotation has been updated successfully");
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

        return $this->success($id, 'The quotation has been deleted successfully');
    }
}
