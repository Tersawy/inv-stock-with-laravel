<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use App\Requests\PurchasePaymentRequest;

class PurchasePaymentController extends Controller
{
  public function create(Request $req, $purchaseId)
  {
    $attr = PurchasePaymentRequest::validationCreate($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    if ($purchase->payment_status == Constants::PAYMENT_STATUS_PAID) {
      return $this->error('This purchase invoice has been paid !', 422);
    }

    $payment = PurchasePayment::create($attr);

    $payment->reference = "INV/PR_" . (1110 + $payment->id);

    $payment->save();

    $purchase->payment_status = $purchase->new_payment_status;

    $purchase->save();

    return $this->success([], 'The Payment has been added successfully');
  }


  public function update(Request $req, $purchaseId, $id)
  {
    $attr = PurchasePaymentRequest::validationUpdate($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    $payment = PurchasePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $payment->amount = $attr['amount'];

    $payment->payment_method = $attr['payment_method'];

    $payment->save();

    $purchase->payment_status = $purchase->new_payment_status;

    $purchase->save();

    return $this->success([], 'The Payment has been updated successfully');
  }


  public function remove(Request $req, $purchaseId, $id)
  {
    PurchasePaymentRequest::validationRemove($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    $payment = PurchasePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $payment->delete();

    $purchase->payment_status = $purchase->new_payment_status;

    $purchase->save();

    return $this->success([], 'The Payment has been deleted successfully');
  }
}
