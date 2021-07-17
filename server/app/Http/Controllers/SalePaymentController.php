<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\SalePayment;
use App\Requests\SalePaymentRequest;

class SalePaymentController extends Controller
{
  public function create(Request $req, $saleId)
  {
    $attr = SalePaymentRequest::validationCreate($req);

    $sale = Sale::find($saleId);

    if (!$sale) return $this->error('The sale invoice is not found to add payment', 404);

    if ($sale->payment_status == 'Paid') {
      return $this->error('This sale invoice has been paid !', 422);
    }

    SalePayment::create($attr);

    return $this->success([], 'The Payment has been added successfully');
  }


  public function update(Request $req, $saleId, $id)
  {
    $attr = SalePaymentRequest::validationUpdate($req);

    $payment = SalePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $payment->amount = $attr['amount'];

    $payment->payment_method = $attr['payment_method'];

    $payment->save();

    return $this->success([], 'The Payment has been updated successfully');
  }


  public function remove(Request $req, $saleId, $id)
  {
    SalePaymentRequest::validationRemove($req);

    $payment = SalePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $payment->delete();

    return $this->success([], 'The Payment has been deleted successfully');
  }
}
