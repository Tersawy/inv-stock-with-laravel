<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PurchaseReturnPayment;

class PurchaseReturnPaymentRequest
{
  protected static function rules()
  {
    return [
      'purchase_id'     => ['required', 'numeric', 'min:1'],
      'amount'          => ['required', 'numeric', 'min:1'],
      'payment_method'  => ['required', Rule::in(Constants::PAYMENT_METHODS)],
      'note'            => ['string', 'max:255', 'nullable']
    ];
  }


  public static function validationCreate(Request $req)
  {
    $rules = PurchaseReturnPaymentRequest::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'purchase_id' => $req->route('purchaseId')]);

    $rules = PurchaseReturnPaymentRequest::rules();

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
