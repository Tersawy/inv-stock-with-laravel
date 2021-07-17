<?php

namespace App\Requests;

use Illuminate\Http\Request;

class CustomerRequest extends ValidateRequest
{
  private static function rules()
  {
    $rules = [
      'name'      => ['required', 'string', 'max:255', 'unique:suppliers'],
      'email'     => ['required', 'email:rfc,dns', 'max:255', 'unique:suppliers'],
      'phone'     => ['required', 'string', 'max:255', 'unique:suppliers'],
      'country'   => ['required', 'string', 'max:255'],
      'city'      => ['required', 'string', 'max:255'],
      'address'   => ['required', 'string', 'max:255'],
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = CustomerRequest::rules();

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = CustomerRequest::rules();

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
