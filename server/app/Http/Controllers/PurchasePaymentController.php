<?php

namespace App\Http\Controllers;

use App\Models\PurchasePayment;
use App\Requests\PurchasePaymentRequest;
use Illuminate\Http\Request;

class PurchasePaymentController extends Controller
{
  public function create(Request $req, $purchaseId)
  {
    $req->merge(['purchase_id' => $purchaseId]);

    $attr = $req->validate(PurchasePaymentRequest::ruleOfCreate());

    PurchasePayment::create($attr);

    return $this->success([], "The Payment has been Added To The Purchase successfully");
  }
}
