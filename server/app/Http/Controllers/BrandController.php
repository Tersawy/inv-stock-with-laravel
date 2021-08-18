<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $searchFields = ['name', 'description'];

    public function index(Request $req)
    {
        $brands = Brand::query();

        $this->handleQuery($req, $brands);

        $brands = $brands->select(['id', 'name', 'description', 'image'])->paginate($req->per_page);

        return $this->success($brands);
    }


    public function options()
    {
        $brands = Brand::all(['id AS value', 'name AS text']);

        return $this->success($brands);
    }


    public function show(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $brand = Brand::find($req->id);

        if (!$brand) return $this->error('The brand was not found', 404);

        return $this->success($brand);
    }


    public function create(Request $req)
    {
        $req->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:brands'],
            'description'   => ['required', 'string', 'max:255'],
            'image'         => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $inputs = $req->only(['name', 'description']);

        $imageName = 'brand_' . date('y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1] . '.' . $req->image->getClientOriginalExtension();

        $req->image->move(public_path('/images/brands'), $imageName);

        $inputs['image'] = $imageName;

        $brand = Brand::create($inputs);

        return $this->success($brand, 'The brand has been created successfully');
    }


    public function update(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate([
            'id'            => ['required', 'numeric', 'min:1'],
            'name'          => ['required', 'string', 'max:255', 'unique:brands,name,' . $req->id],
            'description'   => ['required', 'string', 'max:255'],
            'image'         => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048', 'nullable'],
        ]);

        $inputs = $req->only(['name', 'description']);

        if ($req->image) {
            $imageName = 'brand_' . date('y_m_d_s_') . explode('.', explode(' ', microtime())[0])[1] . '.' . $req->image->getClientOriginalExtension();

            $req->image->move(public_path('/images/brands'), $imageName);

            $inputs['image'] = $imageName;
        }

        $brand = Brand::find($req->id);

        if (!$brand) return $this->error('The brand was not found', 404);

        $brand->fill($inputs);

        $brand->save();

        return $this->success($brand, 'The brand has been updated successfully');
    }


    public function remove(Request $req, $id)
    {
        $req->merge(['id' => $id]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $brand = Brand::find($id);

        if (!$brand) return $this->error('The brand was not found', 404);

        $settings = Setting::where('brand_id', $id)->get();

        if ($settings) return $this->error('The brand cannot be delete because it\'s a default in app settings', 422);

        $brand->delete();

        return $this->success($id, 'The brand has been deleted successfully');
    }
}
