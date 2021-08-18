<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Requests\CustomerRequest;

class CustomerController extends Controller
{
    protected $searchFields = ['code', 'name', 'email', 'phone', 'country', 'city', 'address'];

    public function index(Request $req)
    {
        $customers = Customer::query();

        $this->handleQuery($req, $customers);

        $customers = $customers->select(['code', 'id', 'name', 'email', 'phone', 'country', 'city', 'address'])->paginate($req->per_page);

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

        $customer->code = $customer->id + 100;

        $customer->save();

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


    public function remove(Request $req, $id)
    {
        CustomerRequest::validationId($req);

        $customer = Customer::find($id);

        if (!$customer) return $this->error('The customer was not found', 404);

        $settings = Setting::where('customer_id', $id)->get();

        if ($settings) return $this->error('The customer cannot be delete because it\'s a default in app settings', 422);

        $customer->delete();

        return $this->success($id, 'The customer has been moved to the trash successfully');
    }
}
