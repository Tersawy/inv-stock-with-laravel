<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Traits\PaginationAndSearchOperation;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponser, PaginationAndSearchOperation;
}
