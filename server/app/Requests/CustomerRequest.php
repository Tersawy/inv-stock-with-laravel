<?php

namespace App\Requests;

use Illuminate\Http\Request;

class CustomerRequest extends ValidateRequest
{
  private static function rules(Request $req)
  {
    $unique_field = $req->get('id') ? 'unique:customers,'.$req->id : 'unique:customers';

    $rules = [
      'name'      => ['required', 'string', 'max:255', $unique_field],
      'email'     => ['required', 'email:rfc,dns', 'max:255', $unique_field],
      'phone'     => ['required', 'string', 'max:255', $unique_field],
      'country'   => ['required', 'string', 'max:255'],
      'city'      => ['required', 'string', 'max:255'],
      'address'   => ['required', 'string', 'max:255'],
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = CustomerRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = CustomerRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
