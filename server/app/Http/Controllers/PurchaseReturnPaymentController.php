<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturnPayment;
use App\Requests\PurchaseReturnPaymentRequest;
use Illuminate\Http\Request;

class PurchaseReturnPaymentController extends Controller
{
    public function create(Request $req, $purchaseId)
    {
        $attr = PurchaseReturnPaymentRequest::validationCreate($req);

        $purchase = PurchaseReturnPayment::find($purchaseId);

        if (!$purchase) return $this->error('The purchase return invoice is not found to add payment', 404);

        if ($purchase->payment_status == 'Paid') {
            return $this->error('This purchase return invoice has been paid !', 422);
        }

        PurchaseReturnPayment::create($attr);

        return $this->success([], 'The Payment has been added successfully');
    }


    public function update(Request $req, $purchaseId, $id)
    {
        $attr = PurchaseReturnPaymentRequest::validationUpdate($req);

        $payment = PurchaseReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $payment->amount = $attr['amount'];

        $payment->payment_method = $attr['payment_method'];

        $payment->save();

        return $this->success([], 'The Payment has been updated successfully');
    }


    public function remove(Request $req, $purchaseId, $id)
    {
        PurchaseReturnPaymentRequest::validationRemove($req);

        $payment = PurchaseReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $payment->delete();

        return $this->success([], 'The Payment has been deleted successfully');
    }
}
