<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Helpers\Constants;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use App\Helpers\CustomException;
use Illuminate\Support\Facades\DB;
use App\Requests\SalePaymentRequest;

class SalePaymentController extends Controller
{
  public function index(Request $req, $saleId)
  {
    $req->merge(['saleId' => $saleId]);

    $req->validate(['saleId' => ['required', 'numeric', 'exists:sales,id']]);

    $payments = SalePayment::select(['id', 'reference', 'amount', 'payment_method', 'date'])->where('sale_id', $saleId)->get();

    return $this->success($payments);
  }


  public function create(Request $req, $saleId)
  {
    try {
      DB::transaction(function () use ($req, $saleId) {
        $attr = SalePaymentRequest::validationCreate($req);

        $sale = Sale::find($saleId);

        if (!$sale) return $this->error('The sale invoice is not found to add payment', 404);

        if ($sale->payment_status == Constants::PAYMENT_STATUS_PAID) {
          return $this->error('This sale invoice has been paid !', 422);
        }

        $payment = SalePayment::create($attr);

        $payment->reference = "INV/SL_" . (1110 + $payment->id);

        $payment->save();

        $sale->paid += $payment->amount;

        $sale->payment_status = $sale->new_payment_status;

        $sale->save();
      }, 10);

      return $this->success([], 'The Payment has been added successfully');
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  public function update(Request $req, $saleId, $id)
  {
    try {
      DB::transaction(function () use ($req, $saleId, $id) {
        $attr = SalePaymentRequest::validationUpdate($req);

        $sale = Sale::find($saleId);

        if (!$sale) return $this->error('The sale invoice is not found to add payment', 404);

        $payment = SalePayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $sale->paid = $sale->paid - $payment->amount + $attr['amount'];

        $sale->payment_status = $sale->new_payment_status;

        $payment->fill($attr);

        $payment->save();

        $sale->save();
      }, 10);

      return $this->success([], 'The Payment has been updated successfully');
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }


  public function remove(Request $req, $saleId, $id)
  {
    try {
      DB::transaction(function () use ($req, $saleId, $id) {
        SalePaymentRequest::validationRemove($req);

        $sale = Sale::find($saleId);

        if (!$sale) return $this->error('The sale invoice is not found to add payment', 404);

        $payment = SalePayment::find($id);

        if (!$payment) return $this->error('This payment is not found', 404);

        $payment->delete();

        $sale->paid -= $payment->amount;

        $sale->payment_status = $sale->new_payment_status;

        $sale->save();
      }, 10);

      return $this->success([], 'The Payment has been deleted successfully');
    } catch (CustomException $e) {
      return $this->error($e->first_error(), $e->status_code());
    }
  }
}
