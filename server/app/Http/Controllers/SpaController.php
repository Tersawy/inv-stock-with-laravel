<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;

class SpaController extends Controller
{
    public function index()
    {
        // DB::enableQueryLog();
        $results =  DB::table('product_warehouses')->select(DB::raw('SUM(instock) as total'))->get();

        dd($results);
        // dd(DB::getQueryLog());
        return $this->success($results);
    }
}
