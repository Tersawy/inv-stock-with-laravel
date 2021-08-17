<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchasePaymentRequest
{
  protected static function rules(Request $req)
  {
    $req->merge(['purchase_id' => $req->route('purchaseId')]);

    return [
      'purchase_id'     => ['required', 'numeric', 'min:1'],
      'amount'          => ['required', 'numeric', 'min:1'],
      'payment_method'  => ['required', Rule::in(Constants::PAYMENT_METHODS)],
      'note'            => ['string', 'max:255', 'nullable'],
      'date'            => ['required', 'string', 'max:10', 'date_format:Y-m-d']
    ];
  }


  public static function validationCreate(Request $req)
  {
    $rules = PurchasePaymentRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = PurchasePaymentRequest::rules($req);

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
