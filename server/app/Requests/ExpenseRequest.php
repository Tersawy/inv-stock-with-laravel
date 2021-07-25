<?php

namespace App\Requests;

use Illuminate\Http\Request;

class ExpenseRequest extends ValidateRequest
{

  public static function rules(Request $req)
  {
    $unique_field = $req->get('id') ? 'unique:expenses,'.$req->id : 'unique:expenses';

    $rules = [
      'name'                => ['required', 'string', 'max:255', $unique_field],
      'warehouse_id'        => ['required', 'numeric', 'min:1', 'exists:warehouses,id'],
      'expense_category_id' => ['required', 'numeric', 'min:1', 'exists:expense_categories,id'],
      'amount'              => ['required', 'numeric', 'min:1'],
      'details'             => ['string', 'max:255', 'nullable'],
      'date'                => ['required', 'string', 'max:10', 'date_format:Y-m-d'],
    ];

    return $rules;
  }


  public static function validationCreate(Request $req)
  {
    $rules = ExpenseRequest::rules($req);

    return $req->validate($rules);
  }


  public static function validationUpdate(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $rules = ExpenseRequest::rules($req);

    $rules['id'] = ['required', 'numeric', 'min:1'];

    return $req->validate($rules);
  }
}
