<?php

namespace App\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitRequest extends ValidateRequest
{
  private static function rules(Request $req)
  {
    $unique_field = $req->get('id') ? 'unique:units,' . $req->id : 'unique:units';

    $rules = [
      'name'          => ['required', 'string', 'min:2', 'max:255', $unique_field],
      'short_name'    => ['required', 'string', 'min:2', 'max:255', $unique_field],
      'value'         => [
        'nullable',
        'numeric',
        'min:1',
        Rule::requiredIf(function () use ($req) {
          return (int) $req->get('main_unit_id');
        })
      ],
      'operator'      => ['required', 'string', 'max:255', Rule::in(['/', '*'])],
      'main_unit_id'  => ['nullable', 'numeric', 'min:1']
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = UnitRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = UnitRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
