<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Requests\CustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

        return $this->success($customers);
    }


    public function options()
    {
        $customers = Customer::all(['id AS value', 'name AS text']);

        return $this->success($customers);
    }


    public function show(Request $req)
    {
        CustomerRequest::validationId($req);

        $customer = Customer::find($req->id);

        if (!$customer) return $this->error('The customer was not found', 404);

        return $this->success($customer);
    }


    public function create(Request $req)
    {
        $attr = CustomerRequest::validationCreate($req);

        $customer = Customer::create($attr);

        return $this->success($customer, 'The customer has been created successfully');
    }


    public function update(Request $req)
    {
        $attr = CustomerRequest::validationUpdate($req);

        $customer = Customer::find($req->id);

        if (!$customer) return $this->error('The customer was not found', 404);

        $customer->fill($attr);

        $customer->save();

        return $this->success($customer, 'The customer has been updated successfully');
    }


    public function moveToTrash(Request $req)
    {
        CustomerRequest::validationId($req);

        $customer = Customer::find($req->id);

        if (!$customer) return $this->error('The customer was not found', 404);

        $customer->delete();

        return $this->success($req->id, 'The customer has been moved to the trash successfully');
    }


    public function trashed()
    {
        $customers = Customer::onlyTrashed()->get();

        return $this->success($customers);
    }


    public function restore(Request $req)
    {
        CustomerRequest::validationId($req);

        $isDone = Customer::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The customer is not in the trash', 404);

        return $this->success($req->id, 'The customer has been restored successfully');
    }


    public function remove(Request $req)
    {
        CustomerRequest::validationId($req);

        $isDone = Customer::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The customer is not in the trash', 404);

        return $this->success($req->id, 'The customer has been deleted successfully');
    }
}