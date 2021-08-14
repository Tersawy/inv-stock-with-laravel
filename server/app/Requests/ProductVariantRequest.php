<?php

namespace App\Requests;

use Illuminate\Http\Request;

class ProductVariantRequest extends ValidateRequest
{
  private static function rules(Request $req)
  {
    $req->merge(['productId' => $req->route('productId')]);

    $rules = [
      'name'      => ['required', 'string', 'distinct', 'max:54', 'min:3'],
      'productId' => ['required', 'numeric', 'min:1', 'exists:products,id']
    ];

    if (count($req->get('images', []))) {
      $imagesRule = [
        'images'            => ['required', 'array', 'max:54', 'min:1'],
        'images.*'          => ['required', 'array', 'max:54', 'min:1'],
        'images.*.path'     => ['required', 'base64_image:jpeg,png,jpg'],
        'images.*.default'  => ['numeric', 'min:0', 'max:1'],
      ];

      $rules = array_merge($rules, $imagesRule);
    }

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = ProductVariantRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = ProductVariantRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }


  public static function validationId(Request $req)
  {
    $req->merge(['productId' => $req->route('productId'), 'id' => $req->route('id')]);

    $rules = [
      'id'        => ['required', 'numeric', 'min:1'],
      'productId' => ['required', 'numeric', 'min:1', 'exists:products,id']
    ];

    return $req->validate($rules);
  }


  public static function validationProductId(Request $req)
  {
    $req->merge(['productId' => $req->route('productId')]);

    $rules = ['productId' => ['required', 'numeric', 'min:1', 'exists:products,id']];

    return $req->validate($rules);
  }
}
