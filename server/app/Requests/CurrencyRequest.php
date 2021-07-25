<?php

namespace App\Requests;

use Illuminate\Http\Request;

class CurrencyRequest extends ValidateRequest
{

  public static function rules(Request $req)
  {
    $unique_field = $req->get('id') ? 'unique:currencies,'.$req->id : 'unique:currencies';

    $rules = [
      'name'   => ['required', 'string', 'min:1', 'max:255', $unique_field],
      'code'   => ['required', 'string', 'min:1', 'max:255', $unique_field],
      'symbol' => ['required', 'string', 'min:1', 'max:255'],
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = CurrencyRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = CurrencyRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
