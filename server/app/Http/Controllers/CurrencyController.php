<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Requests\CurrencyRequest;

class CurrencyController extends Controller
{
    protected $searchFields = ['name', 'code', 'symbol'];

    public function index(Request $req)
    {
        $currencies = Currency::query();

        $this->handleQuery($req, $currencies);

        $currencies = $currencies->select(['id', 'name', 'code', 'symbol'])->paginate($req->per_page);

        return $this->success($currencies);
    }


    public function options()
    {
        $currencies = Currency::all(['id AS value', 'name AS text']);

        return $this->success($currencies);
    }


    public function show(Request $req)
    {
        CurrencyRequest::validationId($req);

        $currency = Currency::find($req->id);

        if (!$currency) return $this->error('The currency was not found', 404);

        return $this->success($currency);
    }


    public function create(Request $req)
    {
        $attr = CurrencyRequest::validationCreate($req);

        $currency = Currency::create($attr);

        return $this->success($currency, 'The currency has been created successfully');
    }


    public function update(Request $req)
    {
        $attr = CurrencyRequest::validationUpdate($req);

        $currency = Currency::find($req->id);

        if (!$currency) return $this->error('The currency was not found', 404);

        $currency->fill($attr);

        $currency->save();

        return $this->success($currency, 'The currency has been updated successfully');
    }


    public function moveToTrash(Request $req, $id)
    {
        CurrencyRequest::validationId($req);

        $currency = Currency::find($id);

        if (!$currency) return $this->error('The currency was not found', 404);

        $settings = Setting::where('currency_id', $id)->get();

        if ($settings) return $this->error('The currency cannot be delete because it\'s a default in app settings', 422);

        $currency->delete();

        return $this->success($id, 'The currency has beendeleted successfully');
    }
}
