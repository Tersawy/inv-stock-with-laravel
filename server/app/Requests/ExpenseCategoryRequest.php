<?php

namespace App\Requests;

use Illuminate\Http\Request;

class ExpenseCategoryRequest extends ValidateRequest
{

  public static function rules()
  {
    $rules = [
      'name'        => ['required', 'string', 'min:3', 'max:255'],
      'description' => ['string', 'max:255', 'nullable'],
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = ExpenseCategoryRequest::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = ExpenseCategoryRequest::rules();

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
