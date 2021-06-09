<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'warehouse'], function () {
    Route::get('/', [WarehouseController::class, 'index']);

    Route::get('/options', [WarehouseController::class, 'options']);

    Route::get('/trashed', [WarehouseController::class, 'trashed']);

    Route::get('/{id}', [WarehouseController::class, 'show']);

    Route::put('/{id}', [WarehouseController::class, 'update']);

    Route::post('/create', [WarehouseController::class, 'create']);

    Route::post('/{id}/trash', [WarehouseController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [WarehouseController::class, 'restore']);

    Route::post('/{id}', [WarehouseController::class, 'remove']);
});
