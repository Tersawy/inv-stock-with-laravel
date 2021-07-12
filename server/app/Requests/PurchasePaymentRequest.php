<?php

namespace App\Requests;

use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use Illuminate\Validation\Rule;

class PurchasePaymentRequest
{
  private static function rules()
  {
    return [
      'purchase_id'     => ['required', 'numeric', 'min:1', 'exists:purchases,id'],
      'amount'          => ['required', 'numeric', 'min:1'],
      'payment_method'  => ['required', Rule::in(PurchasePayment::PAYMENT_METHODS)],
      'note'            => ['string', 'max:255', 'nullable']
    ];
  }


  public static function ruleOfCreate()
  {
    $rules = PurchasePaymentRequest::rules();

    return $rules;
  }


  public static function ruleOfUpdate()
  {
    $rules = PurchasePaymentRequest::rules();

    $newRules = ['id' => ['required', 'numeric', 'min:1']];

    return array_merge($rules, $newRules);
  }


  public static function validationRemove(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'purchase_id' => $req->route('purchaseId')]);

    $rule = ['required', 'numeric', 'min:1'];

    $rules = ['id' => $rule, 'purchase_id' => $rule];

    return $req->validate($rules);
  }
}
