<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SubUnit;
use Illuminate\Http\Request;

class SubUnitController extends Controller
{
    public function create(Request $req)
    {
        $attr = $req->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:sub_units'],
            'short_name'    => ['required', 'string', 'max:255', 'unique:sub_units'],
            'value'         => ['required', 'numeric', 'min:1'],
            'main_unit_id'     => ['required', 'numeric', 'min:1', 'exists:main_units,id']
        ]);

        $attr['operator'] = '/';

        $subUnit = SubUnit::create($attr);

        return $this->success($subUnit, "The sub unit has been created successfully");
    }


    public function update(Request $req)
    {
        $req->merge(["id" => $req->route("id")]);

        $attr = $req->validate([
            'id'            => ['required', 'numeric', 'min:1'],
            'name'          => ['required', 'string', 'max:255', 'unique:sub_units,name,' . $req->id],
            'short_name'    => ['required', 'string', 'max:255', 'unique:sub_units,short_name,' . $req->id],
            'main_unit_id'     => ['required', 'numeric', 'min:1', 'exists:main_units,id'],
            'value'         => ['required', 'numeric', 'min:1']
        ]);

        $subUnit = SubUnit::find($req->id);

        if (!$subUnit) return $this->error("The sub unit was not found", 404);

        $subUnit->fill($attr);

        $subUnit->save();

        return $this->success($subUnit, "The sub unit has been updated successfully");
    }


    public function remove(Request $req)
    {
        $req->merge(["id" => $req->route("id")]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $unit = SubUnit::find($req->id);

        if (!$unit) return $this->error("The sub unit was not found", 404);

        $product = Product::where('purchase_unit', $req->id)->orWhere('sale_unit', $req->id)->first();

        if ($product) return $this->error('The sub unit cannot be deleted because there are products that depend on it', 422);

        $unit->delete();

        return $this->success($req->id, "The sub unit has been deleted successfully");
    }
}
