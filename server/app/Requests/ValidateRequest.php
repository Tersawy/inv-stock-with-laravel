<?php

namespace App\Requests;

use Illuminate\Http\Request;

class ValidateRequest
{
  public static function validationId(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = ['id' => ['required', 'numeric', 'min:1']];

    return $req->validate($rules);
  }
}
