<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'currency'], function () {

    Route::get('/', [CurrencyController::class, 'index']);

    Route::post('/create', [CurrencyController::class, 'create']);

    Route::put('/{id}', [CurrencyController::class, 'update']);

    Route::post('/{id}', [CurrencyController::class, 'remove']);
});
