<?php

namespace App\Requests;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductRequest
{

  public static function rules(Request $req)
  {
    $rules = [
      'barcode_type'      => ['required', 'string', 'max:54', 'min:3'],
      'cost'              => ['required', 'numeric', 'min:1'],
      'price'             => ['required', 'numeric', 'min:1', 'gte:cost'],
      'minimum'           => ['required', 'numeric', 'min:0'],
      'tax'               => ['required', 'numeric', 'min:0'],
      'tax_method'        => ['required', Rule::in(Product::TAX_METHODS)],
      'note'              => ['string', 'max:255', 'nullable'],
      'category_id'       => ['required', 'numeric', 'min:1', 'exists:categories,id'],
      'brand_id'          => ['exclude_if:brand_id,0', 'required', 'numeric', 'min:1', 'exists:brands,id'],
      'main_unit_id'      => ['required', 'numeric', 'min:1', 'exists:main_units,id'],
      'purchase_unit_id'  => ['required', 'numeric', 'min:1', 'exists:sub_units,id'],
      'sale_unit_id'      => ['required', 'numeric', 'min:1', 'exists:sub_units,id'],
      'has_variants'      => ['required', 'boolean'],
      'has_images'        => ['required', 'boolean'],
    ];

    $variants_rules = [
      'variants'              => ['required', 'array', 'max:54', 'min:1'],
      'variants.*'            => ['required', 'array', 'max:54', 'min:1'],
      'variants.*.name'       => ['required', 'string', 'distinct', 'max:54', 'min:3']
    ];

    if ($req->has_variants && is_array($req->variants) && count($req->variants)) {
      $rules = array_merge($rules, $variants_rules);
    } else {
      $req->has_variants = false;
    }

    $images_rules = [
      'images'            => ['required', 'array', 'max:54', 'min:1'],
      'images.*'          => ['required', 'array', 'max:54', 'min:1'],
      'images.*.path'     => ['required', 'base64_image:jpeg,png,jpg']
    ];

    if ($req->has_images) {
      $rules = array_merge($rules, $images_rules);
    } else {
      $req->has_images = false;
    }

    return $rules;
  }


  public static function ruleOfCreate(Request $req)
  {
    $rules = ProductRequest::rules($req);

    $newRules = [
      'name' => ['required', 'string', 'max:255', 'min:3', 'unique:products'],
      'code' => ['required', 'string', 'max:54', 'min:3', 'unique:products']
    ];

    return array_merge($rules, $newRules);
  }


  public static function ruleOfUpdate(Request $req)
  {
    $rules = ProductRequest::rules($req);

    $newRules = [
      'id'    => ['required', 'numeric', 'min:1'],
      'name'  => ['required', 'string', 'max:255', 'min:3', 'unique:products,name,' . $req->id],
      'code'  => ['required', 'string', 'max:54', 'min:3', 'unique:products,code,' . $req->id]
    ];

    return array_merge($rules, $newRules);
  }
}