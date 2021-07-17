<?php

namespace App\Requests;

use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use Illuminate\Validation\Rule;
use App\Models\PurchaseReturnPayment;

class PurchaseReturnPaymentRequest
{
  private static function rules()
  {
    return [
      'purchase_id'     => ['required', 'numeric', 'min:1'],
      'amount'          => ['required', 'numeric', 'min:1'],
      'payment_method'  => ['required', Rule::in(PurchasePayment::PAYMENT_METHODS)],
      'note'            => ['string', 'max:255', 'nullable']
    ];
  }


  public static function validationCreate(Request $req)
  {
    $rules = PurchaseReturnPayment::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'purchase_id' => $req->route('purchaseId')]);

    $rules = PurchaseReturnPayment::rules();

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }


  public static function validationRemove(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'purchase_id' => $req->route('purchaseId')]);

    $rule = ['required', 'numeric', 'min:1'];

    $rules = ['id' => $rule, 'purchase_id' => $rule];

    return $req->validate($rules);
  }
}