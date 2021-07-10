<?php

namespace App\Requests;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Validation\Rule;

class PurchaseRequest
{

  public static function rules()
  {
    $rules = [
      'warehouse_id'                => ['required', 'numeric', 'min:1', 'exists:warehouses,id'],
      'supplier_id'                 => ['required', 'numeric', 'min:1', 'exists:suppliers,id'],
      'tax'                         => ['required', 'numeric', 'min:0'],
      'discount'                    => ['required', 'numeric', 'min:0'],
      'discount_method'             => ['required', 'numeric', Rule::in(Product::DISCOUNT_METHODS)],
      'status'                      => ['required', 'numeric', Rule::in(Purchase::STATUS)],
      'shipping'                    => ['required', 'numeric', 'min:0'],
      'note'                        => ['string', 'max:255', 'nullable'],
      'date'                        => ['required', 'string', 'max:10', 'date_format:Y-m-d'],
      // Products Validations
      'products'                    => ['required', 'array', 'min:1'],
      'products.*'                  => ['required', 'array', 'min:1'],
      'products.*.product_id'       => ['required', 'numeric', 'min:1', 'exists:products,id'],
      'products.*.variant_id'       => ['numeric', 'min:1', 'exists:product_variants,id', 'nullable'],
      'products.*.tax'              => ['required', 'numeric', 'min:0'],
      'products.*.tax_method'       => ['required', 'numeric', Rule::in(Product::TAX_METHODS)],
      'products.*.discount'         => ['required', 'numeric', 'min:0'],
      'products.*.discount_method'  => ['required', 'numeric', Rule::in(Product::DISCOUNT_METHODS)],
      'products.*.quantity'         => ['required', 'numeric', 'min:1']
    ];

    return $rules;
  }


  public static function ruleOfCreate()
  {
    $rules = PurchaseRequest::rules();

    return $rules;
  }


  public static function ruleOfUpdate()
  {
    $rules = PurchaseRequest::rules();

    $newRules = ['id' => ['required', 'numeric', 'min:1']];

    return array_merge($rules, $newRules);
  }
}
