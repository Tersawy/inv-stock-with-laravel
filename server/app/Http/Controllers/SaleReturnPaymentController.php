<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use App\Models\SaleReturnPayment;
use App\Requests\SaleReturnPaymentRequest;

class SaleReturnPaymentController extends Controller
{
    public function index(Request $req, $saleId)
    {
        $req->merge(['saleId' => $saleId]);

        $req->validate(['saleId' => ['required', 'numeric', 'exists:sale_returns,id']]);

        $payments = SaleReturnPayment::select(['id', 'reference', 'amount', 'payment_method', 'date'])->where('sale_id', $saleId)->get();

        return $this->success($payments);
    }


    public function create(Request $req, $saleId)
    {
        $attr = SaleReturnPaymentRequest::validationCreate($req);

        $sale = SaleReturn::find($saleId);

        if (!$sale) return $this->error('The sale return invoice is not found to add payment', 404);

        if ($sale->payment_status == Constants::PAYMENT_STATUS_PAID) {
            return $this->error('This sale invoice has been paid !', 422);
        }

        $payment = SaleReturnPayment::create($attr);

        $payment->reference = "INV/RT_" . (1110 + $payment->id);

        $payment->save();

        $sale->paid += $payment->amount;

        $sale->payment_status = $sale->new_payment_status;

        $sale->save();

        return $this->success([], 'The Payment has been added successfully');
    }


    public function update(Request $req, $saleId, $id)
    {
        $attr = SaleReturnPaymentRequest::validationUpdate($req);

        $sale = SaleReturn::find($saleId);

        if (!$sale) return $this->error('The sale return invoice is not found to add payment', 404);

        $payment = SaleReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $sale->paid = $sale->paid - $payment->amount + $attr['amount'];

        $sale->payment_status = $sale->new_payment_status;

        $payment->fill($attr);

        $payment->save();

        $sale->save();

        return $this->success([], 'The Payment has been updated successfully');
    }


    public function remove(Request $req, $saleId, $id)
    {
        SaleReturnPaymentRequest::validationRemove($req);

        $sale = SaleReturn::find($saleId);

        if (!$sale) return $this->error('The sale return invoice is not found to add payment', 404);

        $payment = SaleReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $payment->delete();

        $sale->paid -= $payment->amount;

        $sale->payment_status = $sale->new_payment_status;

        $sale->save();

        return $this->success([], 'The Payment has been deleted successfully');
    }
}
