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
}
