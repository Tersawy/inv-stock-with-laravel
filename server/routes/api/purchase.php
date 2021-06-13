<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'purchase'], function () {
    Route::get('/', [PurchaseController::class, 'index']);

    Route::get('/options', [PurchaseController::class, 'options']);

    Route::get('/{id}', [PurchaseController::class, 'show']);

    Route::put('/{id}', [PurchaseController::class, 'update']);

    Route::post('/create', [PurchaseController::class, 'create']);

    Route::post('/{id}/trash', [PurchaseController::class, 'moveToTrash']);

    Route::get('/trashed', [PurchaseController::class, 'trashed']);

    Route::post('/{id}/restore', [PurchaseController::class, 'restore']);

    Route::post('/{id}', [PurchaseController::class, 'remove']);
});
