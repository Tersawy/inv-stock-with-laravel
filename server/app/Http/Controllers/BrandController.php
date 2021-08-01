<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
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


    public function moveToTrash(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $brand = Brand::find($req->id);

        if (!$brand) return $this->error('The brand was not found', 404);

        $brand->delete();

        return $this->success($req->id, 'The brand has been moved to the trash successfully');
    }


    public function trashed()
    {
        $brands = Brand::onlyTrashed()->get();

        return $this->success($brands);
    }


    public function restore(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $isDone = Brand::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The brand is not in the trash', 404);

        return $this->success($req->id, 'The brand has been restored successfully');
    }


    public function remove(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $isDone = Brand::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The brand is not in the trash', 404);

        $product = Product::where('brand_id', $req->id)->first();

        if ($product) return $this->error('The brand cannot be deleted because there are products that depend on it', 422);

        return $this->success($req->id, 'The brand has been deleted successfully');
    }
}
