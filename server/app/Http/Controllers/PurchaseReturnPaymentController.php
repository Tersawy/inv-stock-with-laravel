<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnPayment;
use App\Requests\PurchaseReturnPaymentRequest;

class PurchaseReturnPaymentController extends Controller
{
    public function index(Request $req, $purchaseId)
    {
        $req->merge(['purchaseId' => $purchaseId]);

        $req->validate(['purchaseId' => ['required', 'numeric', 'exists:purchase_returns,id']]);

        $payments = PurchaseReturnPayment::select(['id', 'reference', 'amount', 'payment_method', 'date'])->where('purchase_return_id', $purchaseId)->get();

        return $this->success($payments);
    }


    public function create(Request $req, $purchaseId)
    {
        $attr = PurchaseReturnPaymentRequest::validationCreate($req);

        $purchase = PurchaseReturn::find($purchaseId);

        if (!$purchase) return $this->error('The purchase return invoice is not found to add payment', 404);

        if ($purchase->payment_status == Constants::PAYMENT_STATUS_PAID) {
            return $this->error('This purchase invoice has been paid !', 422);
        }

        $payment = PurchaseReturnPayment::create($attr);

        $payment->reference = "INV/RT_" . (1110 + $payment->id);

        $payment->save();

        $purchase->paid += $payment->amount;

        $purchase->payment_status = $purchase->new_payment_status;

        $purchase->save();

        return $this->success([], 'The Payment has been added successfully');
    }


    public function update(Request $req, $purchaseId, $id)
    {
        $attr = PurchaseReturnPaymentRequest::validationUpdate($req);

        $purchase = PurchaseReturn::find($purchaseId);

        if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

        $payment = PurchaseReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $purchase->paid = $purchase->paid - $payment->amount + $attr['amount'];

        $purchase->payment_status = $purchase->new_payment_status;

        $payment->fill($attr);

        $payment->save();

        $purchase->save();

        return $this->success([], 'The Payment has been updated successfully');
    }


    public function remove(Request $req, $purchaseId, $id)
    {
        PurchaseReturnPaymentRequest::validationRemove($req);

        $purchase = PurchaseReturn::find($purchaseId);

        if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

        $payment = PurchaseReturnPayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $purchase->paid -= $payment->amount;

        $purchase->payment_status = $purchase->new_payment_status;

        $payment->delete();

        $purchase->save();

        return $this->success([], 'The Payment has been deleted successfully');
    }
}
