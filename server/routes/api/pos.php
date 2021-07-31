<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'pos'], function () {

    Route::post('/create', [PosController::class, 'create']);

});
