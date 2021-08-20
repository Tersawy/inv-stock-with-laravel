<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ProductWarehouse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SpaController extends Controller
{
    public function index()
    {
        // DB::enableQueryLog();
        // $results =  DB::table('product_warehouses')->select(DB::raw('SUM(instock) as total'))->get();
        $data = [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2', 'price' => 300],
        ];

        Arr::forget($data, '*.price');

        dd($data);
        // dd(DB::getQueryLog());
        return $this->success($data);
    }
}
