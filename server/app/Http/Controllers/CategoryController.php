<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

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


    public function moveToTrash(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $category = Category::find($req->id);

        if (!$category) return $this->error('The category was not found', 404);

        $category->delete();

        return $this->success($req->id, 'The category has been moved to the trash successfully');
    }


    public function trashed()
    {
        $categories = Category::onlyTrashed()->get();

        return $this->success($categories);
    }


    public function restore(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $isDone = Category::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The category is not in the trash', 404);

        return $this->success($req->id, 'The category has been restored successfully');
    }


    public function remove(Request $req)
    {
        $req->merge(['id' => $req->route('id')]);

        $req->validate(['id' => ['required', 'numeric', 'min:1']]);

        $isDone = Category::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The category is not in the trash', 404);

        return $this->success($req->id, 'The category has been deleted successfully');
    }
}
