<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdjustmentRequest extends ValidateRequest
{

  public static function rules()
  {
    $rules = [
      'warehouse_id'           => ['required', 'numeric', 'min:1', 'exists:warehouses,id'],
      'note'                   => ['string', 'max:255', 'nullable'],
      'date'                   => ['required', 'string', 'max:10', 'date_format:Y-m-d'],
      'status'                 => ['required', 'numeric', Rule::in(Constants::ADJUSTMENT_STATUS)],
      // Products Validations
      'products'               => ['required', 'array', 'min:1'],
      'products.*'             => ['required', 'array', 'min:1'],
      'products.*.product_id'  => ['required', 'numeric', 'min:1', 'exists:products,id'],
      'products.*.variant_id'  => ['numeric', 'min:1', 'exists:product_variants,id', 'nullable'],
      'products.*.type'        => ['required', 'numeric', Rule::in(Constants::ADJUSTMENT_DETAILS_STATUS)],
      'products.*.quantity'    => ['required', 'numeric', 'min:1']
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = AdjustmentRequest::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = AdjustmentRequest::rules();

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
