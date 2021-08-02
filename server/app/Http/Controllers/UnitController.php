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
            $query->select(['id AS value', 'name AS text', 'main_unit_id']);
        }];

        $units = Unit::where('main_unit_id', null)->with($sub_units)->get(['id', 'id AS value', 'name AS text']);

        return $this->success($units);
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
