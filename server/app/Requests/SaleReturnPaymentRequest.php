<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SaleReturnPayment;

class SaleReturnPaymentRequest
{
  protected static function rules()
  {
    return [
      'sale_id'         => ['required', 'numeric', 'min:1'],
      'amount'          => ['required', 'numeric', 'min:1'],
      'payment_method'  => ['required', Rule::in(Constants::PAYMENT_METHODS)],
      'note'            => ['string', 'max:255', 'nullable']
    ];
  }


  public static function validationCreate(Request $req)
  {
    $rules = SaleReturnPaymentRequest::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id'), 'sale_id' => $req->route('saleId')]);

    $rules = SaleReturnPaymentRequest::rules();

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
