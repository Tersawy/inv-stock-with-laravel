<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
  public function index()
  {
    $suppliers = Supplier::all();

    return $this->success($suppliers);
  }


  public function options()
  {
    $suppliers = Supplier::all(['id AS value', 'name AS text']);

    return $this->success($suppliers);
  }


  public function show(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $supplier = Supplier::find($req->id);

    if (!$supplier) return $this->error('The supplier was not found', 404);

    return $this->success($supplier);
  }


  public function create(Request $req)
  {
    $attr = $req->validate([
      'name'      => ['required', 'string', 'max:255', 'unique:suppliers'],
      'email'     => ['required', 'email:rfc,dns', 'max:255', 'unique:suppliers'],
      'phone'     => ['required', 'string', 'max:255', 'unique:suppliers'],
      'country'   => ['required', 'string', 'max:255'],
      'city'      => ['required', 'string', 'max:255'],
      'address'   => ['required', 'string', 'max:255'],
    ]);

    $supplier = Supplier::create($attr);

    return $this->success($supplier, 'The supplier has been created successfully');
  }


  public function update(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate([
      'id'        => ['required', 'numeric', 'min:1'],
      'name'      => ['required', 'string', 'max:255', 'unique:suppliers,name,' . $req->id],
      'email'     => ['required', 'email:rfc,dns', 'max:255', 'unique:suppliers,email,' . $req->id],
      'phone'     => ['required', 'string', 'max:255', 'unique:suppliers,phone,' . $req->id],
      'country'   => ['required', 'string', 'max:255'],
      'city'      => ['required', 'string', 'max:255'],
      'address'   => ['required', 'string', 'max:255'],
    ]);

    $inputs = $req->only(['name', 'phone', 'country', 'city', 'address', 'email']);

    $supplier = Supplier::find($req->id);

    if (!$supplier) return $this->error('The supplier was not found', 404);

    $supplier->fill($inputs);

    $supplier->save();

    return $this->success($supplier, 'The supplier has been updated successfully');
  }


  public function moveToTrash(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $supplier = Supplier::find($req->id);

    if (!$supplier) return $this->error('The supplier was not found', 404);

    $supplier->delete();

    return $this->success($req->id, 'The supplier has been moved to the trash successfully');
  }


  public function trashed()
  {
    $suppliers = Supplier::onlyTrashed()->get();

    return $this->success($suppliers);
  }


  public function restore(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $isDone = Supplier::onlyTrashed()->where('id', $req->id)->restore();

    if (!$isDone) return $this->error('The supplier is not in the trash', 404);

    return $this->success($req->id, 'The supplier has been restored successfully');
  }


  public function remove(Request $req)
  {
    $req->merge(['id' => $req->route('id')]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $isDone = Supplier::onlyTrashed()->where('id', $req->id)->forceDelete();

    if (!$isDone) return $this->error('The supplier is not in the trash', 404);

    return $this->success($req->id, 'The supplier has been deleted successfully');
  }
}
