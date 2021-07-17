<?php

namespace App\Requests;

use App\Helpers\Constants;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SaleRequest extends ValidateRequest
{

  public static function rules()
  {
    $rules = [
      'warehouse_id'                => ['required', 'numeric', 'min:1', 'exists:warehouses,id'],
      'customer_id'                 => ['required', 'numeric', 'min:1', 'exists:customers,id'],
      'tax'                         => ['required', 'numeric', 'min:0'],
      'discount'                    => ['required', 'numeric', 'min:0'],
      'discount_method'             => ['required', 'numeric', Rule::in(Constants::DISCOUNT_METHODS)],
      'status'                      => ['required', 'numeric', Rule::in(Constants::INVOICE_STATUS)],
      'shipping'                    => ['required', 'numeric', 'min:0'],
      'note'                        => ['string', 'max:255', 'nullable'],
      'date'                        => ['required', 'string', 'max:10', 'date_format:Y-m-d'],
      // Products Validations
      'products'                    => ['required', 'array', 'min:1'],
      'products.*'                  => ['required', 'array', 'min:1'],
      'products.*.product_id'       => ['required', 'numeric', 'min:1', 'exists:products,id'],
      'products.*.variant_id'       => ['numeric', 'min:1', 'exists:product_variants,id', 'nullable'],
      'products.*.price'            => ['required', 'numeric', 'min:1'],
      'products.*.tax'              => ['required', 'numeric', 'min:0'],
      'products.*.tax_method'       => ['required', 'numeric', Rule::in(Constants::TAX_METHODS)],
      'products.*.discount'         => ['required', 'numeric', 'min:0'],
      'products.*.discount_method'  => ['required', 'numeric', Rule::in(Constants::DISCOUNT_METHODS)],
      'products.*.quantity'         => ['required', 'numeric', 'min:1']
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = SaleRequest::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = SaleRequest::rules();

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
