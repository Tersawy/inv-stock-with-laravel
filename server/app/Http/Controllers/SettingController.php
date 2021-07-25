<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $customer = ['customer' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $warehouse = ['warehouse' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $currency = ['currency' => function ($query) {
            $query->select(['id', 'name']);
        }];

        $with_fields = array_merge([], $customer, $warehouse, $currency);
        
        $settings = Setting::with($with_fields)->get()->first();

        return $this->success($settings);
    }
}
