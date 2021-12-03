<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'suppliers'], function () {
    Route::get('/', [SupplierController::class, 'index']);

    Route::get('/options', [SupplierController::class, 'options']);

    Route::get('/{id}', [SupplierController::class, 'show']);

    Route::put('/{id}', [SupplierController::class, 'update']);

    Route::post('/create', [SupplierController::class, 'create']);

    Route::post('/{id}', [SupplierController::class, 'remove']);
});
