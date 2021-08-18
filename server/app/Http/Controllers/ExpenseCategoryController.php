<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Requests\ExpenseCategoryRequest;

class ExpenseCategoryController extends Controller
{
    protected $searchFields = ['name', 'description'];

    public function index(Request $req)
    {
        $categories = ExpenseCategory::query();

        $this->handleQuery($req, $categories);

        $categories = $categories->select(['id', 'name', 'description'])->paginate($req->per_page);

        return $this->success($categories);
    }


    public function options()
    {
        $categories = ExpenseCategory::all(['id AS value', 'name AS text']);

        return $this->success($categories);
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


    public function remove(Request $req, $id)
    {
        ExpenseCategoryRequest::validationId($req);

        $category = ExpenseCategory::find($id);

        if (!$category) return $this->error('The expense category was not found', 404);

        $expenses = Expense::where('expense_id', $id)->limit(1)->get();

        if ($expenses) return $this->error('The category cannot be delete because it have expenses', 422);

        $category->delete();

        return $this->success($id, 'The expense category has been deleted successfully');
    }
}
