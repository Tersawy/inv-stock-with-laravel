<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SubUnit;
use App\Models\MainUnit;
use Illuminate\Http\Request;

class MainUnitController extends Controller
{
    public function index()
    {
        $units = MainUnit::with(["sub_units" => function ($query) {
            $query->select(['id', 'name', 'short_name', 'value', 'main_unit_id']);
        }])->get(['id', 'name', 'short_name', 'value']);

        return $this->success($units);
    }


    public function options()
    {
        $units = MainUnit::with(["sub_units" => function ($query) {
            $query->select(['id AS value', 'name AS text', 'main_unit_id']);
        }])->get(['id', 'id AS value', 'name AS text']);

        return $this->success($units);
    }


    public function create(Request $req)
    {
        $attr = $req->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:main_units'],
            'short_name'    => ['required', 'string', 'max:255', 'unique:main_units']
        ]);

        $attr['value'] = 1;
        $attr['operator'] = '/';

        $mainUnit = MainUnit::create($attr);

        $attr['operator'] = '*';
        $attr['main_unit_id'] = $mainUnit->id;

        $subUnit = SubUnit::create($attr);

        $mainUnit->sub_units = [$subUnit];

        return $this->success($mainUnit, 'The main unit has been created successfully');
    }


    public function update(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $attr = $req->validate([
            'id'            => ['required', 'numeric', 'min:1'],
            'name'          => ['required', 'string', 'max:255', 'unique:main_units,name,' . $req->id],
            'short_name'    => ['required', 'string', 'max:255', 'unique:main_units,short_name,' . $req->id]
        ]);

        $mainUnit = MainUnit::find($req->id);

        if (!$mainUnit) return $this->error('The main unit was not found', 404);

        $mainUnit->fill($attr);

        $mainUnit->save();

        return $this->success($mainUnit, 'The main unit has been updated successfully');
    }


    public function remove(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $unit = MainUnit::find($req->id);

        if (!$unit) return $this->error('The main unit was not found', 404);

        $unit = SubUnit::where('main_unit_id', $req->id)->first();

        if ($unit) return $this->error('The main unit cannot be deleted because it contains sub-unit', 422);

        $product = Product::where('main_unit_id', $req->id)->first();

        if ($product) return $this->error('The main unit cannot be deleted because there are products that depend on it', 422);

        $unit->delete();

        return $this->success($req->id, 'The main unit has been deleted successfully');
    }
}
