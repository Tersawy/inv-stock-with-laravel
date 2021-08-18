<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'customer'], function () {
    Route::get('/', [CustomerController::class, 'index']);

    Route::get('/options', [CustomerController::class, 'options']);

    Route::get('/{id}', [CustomerController::class, 'show']);

    Route::put('/{id}', [CustomerController::class, 'update']);

    Route::post('/create', [CustomerController::class, 'create']);

    Route::post('/{id}', [CustomerController::class, 'remove']);
});
