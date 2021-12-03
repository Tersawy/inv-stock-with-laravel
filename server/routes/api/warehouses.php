<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'warehouses'], function () {
    Route::get('/', [WarehouseController::class, 'index']);

    Route::get('/options', [WarehouseController::class, 'options']);

    Route::get('/{id}', [WarehouseController::class, 'show']);

    Route::put('/{id}', [WarehouseController::class, 'update']);

    Route::post('/create', [WarehouseController::class, 'create']);

    Route::post('/{id}', [WarehouseController::class, 'remove']);
});
