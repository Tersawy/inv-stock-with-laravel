<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use App\Requests\PurchasePaymentRequest;

class PurchasePaymentController extends Controller
{
  public function create(Request $req, $purchaseId)
  {
    $req->merge(['purchase_id' => $purchaseId]);

    $attr = $req->validate(PurchasePaymentRequest::ruleOfCreate());

    $purchase = Purchase::find($purchaseId);

    if ($purchase->payment_status == 'Paid') {
      return $this->error('This purchase invoice has been paid !', 422);
    }

    PurchasePayment::create($attr);

    return $this->success([], 'The Payment has been added successfully');
  }


  public function update(Request $req, $purchaseId, $id)
  {
    $req->merge(['id' => $id, 'purchase_id' => $purchaseId]);

    $attr = $req->validate(PurchasePaymentRequest::ruleOfUpdate());

    $payment = PurchasePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $payment->amount = $attr['amount'];

    $payment->payment_method = $attr['payment_method'];

    $payment->save();

    return $this->success([], 'The Payment has been updated successfully');
  }


  public function remove(Request $req, $purchaseId, $id)
  {
    PurchasePaymentRequest::validationRemove($req);

    $payment = PurchasePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $payment->delete();

    return $this->success([], 'The Payment has been deleted successfully');
  }
}
