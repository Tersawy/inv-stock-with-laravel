<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SaleReturnPaymentRequest
{
  protected static function rules(Request $req)
  {
    $req->merge(['sale_id' => $req->route('saleId')]);

    return [
      'sale_id'         => ['required', 'numeric', 'min:1'],
      'amount'          => ['required', 'numeric', 'min:1'],
      'payment_method'  => ['required', Rule::in(Constants::PAYMENT_METHODS)],
      'note'            => ['string', 'max:255', 'nullable']
    ];
  }


  public static function validationCreate(Request $req)
  {
    $rules = SaleReturnPaymentRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = SaleReturnPaymentRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }


  public static function validationRemove(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'sale_id' => $req->route('saleId')]);

    $rule = ['required', 'numeric', 'min:1'];

    $rules = ['id' => $rule, 'sale_id' => $rule];

    return $req->validate($rules);
  }
}
