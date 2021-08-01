<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Requests\CurrencyRequest;
use Illuminate\Http\Request;

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


    public function moveToTrash(Request $req)
    {
        CurrencyRequest::validationId($req);

        $currency = Currency::find($req->id);

        if (!$currency) return $this->error('The currency was not found', 404);

        $currency->delete();

        return $this->success($req->id, 'The currency has been moved to the trash successfully');
    }


    public function trashed()
    {
        $currencies = Currency::onlyTrashed()->get();

        return $this->success($currencies);
    }


    public function restore(Request $req)
    {
        CurrencyRequest::validationId($req);

        $isDone = Currency::onlyTrashed()->where('id', $req->id)->restore();

        if (!$isDone) return $this->error('The currency is not in the trash', 404);

        return $this->success($req->id, 'The currency has been restored successfully');
    }


    public function remove(Request $req)
    {
        CurrencyRequest::validationId($req);

        $isDone = Currency::onlyTrashed()->where('id', $req->id)->forceDelete();

        if (!$isDone) return $this->error('The currency is not in the trash', 404);

        return $this->success($req->id, 'The currency has been deleted successfully');
    }
}
