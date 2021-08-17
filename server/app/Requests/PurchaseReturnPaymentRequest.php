<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchaseReturnPaymentRequest
{
  protected static function rules(Request $req)
  {
    $req->merge(['purchase_return_id' => $req->route('purchaseId')]);

    return [
      'purchase_return_id'  => ['required', 'numeric', 'min:1'],
      'amount'              => ['required', 'numeric', 'min:1'],
      'payment_method'      => ['required', Rule::in(Constants::PAYMENT_METHODS)],
      'note'                => ['string', 'max:255', 'nullable'],
      'date'                => ['required', 'string', 'max:10', 'date_format:Y-m-d']
    ];
  }


  public static function validationCreate(Request $req)
  {
    $rules = PurchaseReturnPaymentRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = PurchaseReturnPaymentRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }


  public static function validationRemove(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'purchase_return_id' => $req->route('purchaseId')]);

    $rule = ['required', 'numeric', 'min:1'];

    $rules = ['id' => $rule, 'purchase_return_id' => $rule];

    return $req->validate($rules);
  }
}
