<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $searchFields = ['name', 'code'];

    public function index(Request $req)
    {
        $categories = Category::query();

        $this->handleQuery($req, $categories);

        $categories = $categories->select(['id', 'name', 'code'])->paginate($req->per_page);

        return $this->success($categories);
    }


    public function options()
    {
        $categories = Category::all(['id AS value', 'name AS text']);

        return $this->success($categories);
    }


    public function show(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $category = Category::find($req->id);

        if (!$category) return $this->error('The category was not found', 404);

        return $this->success($category);
    }


    public function create(Request $req)
    {
        $attr = $req->validate([
            'name'      => ['required', 'string', 'max:255', 'unique:categories'],
            'code'      => ['required', 'string', 'max:255', 'unique:categories'],
        ]);

        $category = Category::create($attr);

        return $this->success($category, 'The category has been created successfully');
    }


    public function update(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $attr = $req->validate([
            'id'        => ['required', 'numeric', 'min:1'],
            'name'      => ['required', 'string', 'max:255', 'unique:categories,name,' . $req->id],
            'code'      => ['required', 'string', 'max:255', 'unique:categories,code,' . $req->id],
        ]);

        $category = Category::find($req->id);

        if (!$category) return $this->error('The category was not found', 404);

        $category->fill($attr);

        $category->save();

        return $this->success($category, 'The category has been updated successfully');
    }


    public function remove(Request $req, $id)
    {
        $req->merge(['id' => $id]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $category = Category::find($id);

        if (!$category) return $this->error('The category was not found', 404);

        $products = Product::where('category_id', $id)->limit(1)->get();

        if ($products) return $this->error('The category cannot be delete because it have products', 422);

        $category->delete();

        return $this->success($id, 'The category has been deleted successfully');
    }
}
