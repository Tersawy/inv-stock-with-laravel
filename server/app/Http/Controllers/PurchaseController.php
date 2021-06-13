<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function create(Request $req)
    {
        $attr = $req->validate(PurchaseRequest::ruleOfCreate());

        $this->success($attr);
    }
}
