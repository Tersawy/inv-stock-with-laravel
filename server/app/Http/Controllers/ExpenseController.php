<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Requests\ExpenseRequest;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::all();

        return $this->success($expenses);
    }


    public function show(Request $req)
    {
        ExpenseRequest::validationId($req);

        $expense = Expense::find($req->id);

        if (!$expense) return $this->error('The expense was not found', 404);

        return $this->success($expense);
    }


    public function create(Request $req)
    {
        $attr = ExpenseRequest::validationCreate($req);

        $expense = Expense::create($attr);

        return $this->success($expense, 'The expense has been created successfully');
    }


    public function update(Request $req)
    {
        $attr = ExpenseRequest::validationUpdate($req);

        $expense = Expense::find($req->id);

        if (!$expense) return $this->error('The expense was not found', 404);

        $expense->fill($attr);

        $expense->save();

        return $this->success($expense, 'The expense has been updated successfully');
    }


    public function moveToTrash(Request $req)
    {
        ExpenseRequest::validationId($req);

        $expense = Expense::find($req->id);

        if (!$expense) return $this->error('The expense was not found', 404);

        $expense->delete();

        return $this->success($req->id, 'The expense has been moved to the trash successfully');
    }


    public function trashed()
    {
        $expenses = Expense::onlyTrashed()->get();

        return $this->success($expenses);
    }


    public function restore(Request $req)
    {
        ExpenseRequest::validationId($req);

        $isDone = Expense::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The expense is not in the trash', 404);

        return $this->success($req->id, 'The expense has been restored successfully');
    }


    public function remove(Request $req)
    {
        ExpenseRequest::validationId($req);

        $isDone = Expense::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The expense is not in the trash', 404);

        return $this->success($req->id, 'The expense has been deleted successfully');
    }
}
