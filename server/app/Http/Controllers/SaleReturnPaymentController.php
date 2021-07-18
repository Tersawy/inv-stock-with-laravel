<?php

namespace App\Http\Controllers;

use App\Models\SaleReturnPayment;
use App\Requests\SaleReturnPaymentRequest;
use Illuminate\Http\Request;

class SaleReturnPaymentController extends Controller
{
    public function create(Request $req, $saleId)
    {
        $attr = SaleReturnPaymentRequest::validationCreate($req);

        $sale = SaleReturnPayment::find($saleId);

        if (!$sale) return $this->error('The sale return invoice is not found to add payment', 404);

        if ($sale->payment_status == 'Paid') {
            return $this->error('This sale return invoice has been paid !', 422);
        }

        SaleReturnPayment::create($attr);

        return $this->success([], 'The Payment has been added successfully');
    }


    public function update(Request $req, $saleId, $id)
    {
        $attr = SaleReturnPaymentRequest::validationUpdate($req);

        $payment = SaleReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $payment->amount = $attr['amount'];

        $payment->payment_method = $attr['payment_method'];

        $payment->save();

        return $this->success([], 'The Payment has been updated successfully');
    }


    public function remove(Request $req, $saleId, $id)
    {
        SaleReturnPaymentRequest::validationRemove($req);

        $payment = SaleReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $payment->delete();

        return $this->success([], 'The Payment has been deleted successfully');
    }
}
