<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Traits\ProductWarehouseOperations;

class WarehouseController extends Controller
{
    use ProductWarehouseOperations;

    protected $searchFields = ['name', 'phone', 'country', 'city', 'email', 'zip_code'];

    public function index(Request $req)
    {
        $warehouses = Warehouse::query();

        $this->handleQuery($req, $warehouses);

        $warehouses = $warehouses->select(['id', 'name', 'phone', 'country', 'city', 'email', 'zip_code'])->paginate($req->per_page);

        return $this->success($warehouses);
    }


    public function options()
    {
        $warehouses = Warehouse::all(['id AS value', 'name AS text']);

        return $this->success($warehouses);
    }


    public function show(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $warehouse = Warehouse::find($req->id);

        if (!$warehouse) return $this->error('The warehouse was not found', 404);

        return $this->success($warehouse);
    }


    public function create(Request $req)
    {
        $attr = $req->validate([
            'name'      => ['required', 'string', 'max:255', 'unique:warehouses'],
            'phone'     => ['required', 'string', 'max:255'],
            'country'   => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:255'],
            'zip_code'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        $warehouse = Warehouse::create($attr);

        $this->addAllProductsToWarehouse($warehouse);

        return $this->success($warehouse, 'The warehouse has been created successfully');
    }


    public function update(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $attr = $req->validate([
            'id'        => ['required', 'numeric', 'min:1'],
            'name'      => ['required', 'string', 'max:255', 'unique:warehouses,name,' . $req->id],
            'phone'     => ['required', 'string', 'max:255'],
            'country'   => ['required', 'string', 'max:255'],
            'city'      => ['required', 'string', 'max:255'],
            'zip_code'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email:rfc,dns', 'max:255'],
        ]);

        $warehouse = Warehouse::find($req->id);

        if (!$warehouse) return $this->error('The warehouse was not found', 404);

        $warehouse->fill($attr);

        $warehouse->save();

        return $this->success($warehouse, 'The warehouse has been updated successfully');
    }


    public function remove(Request $req, $id)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $warehouse = Warehouse::find($id);

        if (!$warehouse) return $this->error('The warehouse was not found', 404);

        $settings = Setting::where('warehouse_id', $id)->get();

        if ($settings) return $this->error('The warehouse cannot be delete because it\'s a default in app settings', 422);

        $products = DB::table('product_warehouses')->where('warehouse_id', $id)->select(DB::raw('SUM(instock) as total'))->get();

        if (!$products->total > 0) return $this->error('The warehouse cannot be delete because it has products with quantity', 422);

        $warehouse->delete();

        return $this->success($id, 'The warehouse has deleted successfully');
    }
}
