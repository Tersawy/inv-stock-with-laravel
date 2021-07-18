<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Requests\ExpenseCategoryRequest;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::all();

        return $this->success($categories);
    }


    public function options()
    {
        $categories = ExpenseCategory::all(['id AS value', 'name AS text']);

        return $this->success($categories);
    }


    public function show(Request $req)
    {
        ExpenseCategoryRequest::validationId($req);

        $category = ExpenseCategory::find($req->id);

        if (!$category) return $this->error('The expense category was not found', 404);

        return $this->success($category);
    }


    public function create(Request $req)
    {
        $attr = ExpenseCategoryRequest::validationCreate($req);

        $category = ExpenseCategory::create($attr);

        return $this->success($category, 'The expense category has been created successfully');
    }


    public function update(Request $req)
    {
        $attr = ExpenseCategoryRequest::validationUpdate($req);

        $category = ExpenseCategory::find($req->id);

        if (!$category) return $this->error('The expense category was not found', 404);

        $category->fill($attr);

        $category->save();

        return $this->success($category, 'The expense category has been updated successfully');
    }


    public function moveToTrash(Request $req)
    {
        ExpenseCategoryRequest::validationId($req);

        $category = ExpenseCategory::find($req->id);

        if (!$category) return $this->error('The expense category was not found', 404);

        $category->delete();

        return $this->success($req->id, 'The expense category has been moved to the trash successfully');
    }


    public function trashed()
    {
        $categories = ExpenseCategory::onlyTrashed()->get();

        return $this->success($categories);
    }


    public function restore(Request $req)
    {
        ExpenseCategoryRequest::validationId($req);

        $isDone = ExpenseCategory::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The expense category is not in the trash', 404);

        return $this->success($req->id, 'The expense category has been restored successfully');
    }


    public function remove(Request $req)
    {
        ExpenseCategoryRequest::validationId($req);

        $isDone = ExpenseCategory::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The expense category is not in the trash', 404);

        return $this->success($req->id, 'The expense category has been deleted successfully');
    }
}
