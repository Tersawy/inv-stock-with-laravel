<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Unit;
use App\Requests\UnitRequest;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function create(Request $req)
    {
        $attr = UnitRequest::validationCreate($req);

        $unit = Unit::create($attr);

        return $this->success($unit, "The unit has been created successfully");
    }


    public function options()
    {
        $sub_units = ["sub_units" => function ($query) {
            $query->select(['id', 'name', 'main_unit_id']);
        }];

        $units = Unit::where('main_unit_id', null)->with($sub_units)->get(['id', 'name']);

        $results = [];

        foreach ($units as $unit) {
            $_unit = [
                'value' => $unit->id,
                'text' => $unit->name,
                'sub_units' => [['value' => $unit->id, 'text' => $unit->name]]
            ];

            if (count($unit->sub_units)) {
                foreach ($unit->sub_units as $sub_unit) {
                    $_unit['sub_units'][] = ['value' => $sub_unit->id, 'text' => $sub_unit->name];
                }
            }

            $results[] = $_unit;
        }

        return $this->success($results);
    }


    public function update(Request $req)
    {
        $attr = UnitRequest::validationUpdate($req);

        $unit = Unit::find($req->id);

        if (!$unit) return $this->error("The unit was not found", 404);

        $unit->fill($attr);

        $unit->save();

        return $this->success($unit, "The unit has been updated successfully");
    }


    public function remove(Request $req)
    {
        UnitRequest::validationId($req);

        $unit = Unit::find($req->id);

        if (!$unit) return $this->error("The unit was not found", 404);

        $product = Product::where('purchase_unit', $req->id)->orWhere('sale_unit', $req->id)->limit(1)->get('id');

        if ($product) return $this->error('The unit cannot be deleted because there are products that depend on it', 422);

        $unit->delete();

        return $this->success($req->id, "The unit has been deleted successfully");
    }
}
