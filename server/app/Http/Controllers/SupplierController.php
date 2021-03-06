<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
  protected $searchFields = ['code', 'name', 'email', 'phone', 'country', 'city', 'address'];

  public function index(Request $req)
  {
    $suppliers = Supplier::query();

    $this->handleQuery($req, $suppliers);

    $suppliers = $suppliers->select(['id', 'code', 'name', 'email', 'phone', 'country', 'city', 'address'])->paginate($req->per_page);

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

    $supplier->code = $supplier->id + 100;

    $supplier->save();

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


  public function remove(Request $req, $id)
  {
    $req->merge(['id' => $id]);

    $req->validate(['id' => ['required', 'numeric', 'min:1']]);

    $supplier = Supplier::find($id);

    if (!$supplier) return $this->error('The supplier was not found', 404);

    $supplier->delete();

    return $this->success($id, 'The supplier has been deleted successfully');
  }
}
