<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchasePaymentController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'purchases'], function () {

    Route::get('/', [PurchaseController::class, 'index']);

    Route::get('/trashed', [PurchaseController::class, 'trashed']);

    Route::get('/{id}', [PurchaseController::class, 'show']);

    Route::post('/create', [PurchaseController::class, 'create']);

    Route::get('/{id}/edit', [PurchaseController::class, 'edit']);

    Route::put('/{id}', [PurchaseController::class, 'update']);

    Route::post('/{id}/trash', [PurchaseController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [PurchaseController::class, 'restore']);

    Route::post('/{id}', [PurchaseController::class, 'remove']);
});

############################################# Purchase Payment #############################################

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'purchases/{purchaseId}/payments'], function () {

    Route::get('/', [PurchasePaymentController::class, 'index']);

    Route::get('/{id}', [PurchasePaymentController::class, 'show']);

    Route::post('/create', [PurchasePaymentController::class, 'create']);

    Route::put('/{id}', [PurchasePaymentController::class, 'update']);

    Route::post('/{id}', [PurchasePaymentController::class, 'remove']);
});
