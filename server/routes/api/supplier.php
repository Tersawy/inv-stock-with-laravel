<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'supplier'], function () {
    Route::get('/', [SupplierController::class, 'index']);

    Route::get('/options', [SupplierController::class, 'options']);

    Route::get('/trashed', [SupplierController::class, 'trashed']);

    Route::get('/{id}', [SupplierController::class, 'show']);

    Route::put('/{id}', [SupplierController::class, 'update']);

    Route::post('/create', [SupplierController::class, 'create']);

    Route::post('/{id}/trash', [SupplierController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [SupplierController::class, 'restore']);

    Route::post('/{id}', [SupplierController::class, 'remove']);
});
