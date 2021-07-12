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
    $attr = PurchasePaymentRequest::validationCreate($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    if ($purchase->payment_status == 'Paid') {
      return $this->error('This purchase invoice has been paid !', 422);
    }

    PurchasePayment::create($attr);

    return $this->success([], 'The Payment has been added successfully');
  }


  public function update(Request $req, $purchaseId, $id)
  {
    $attr = PurchasePaymentRequest::validationUpdate($req);

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
