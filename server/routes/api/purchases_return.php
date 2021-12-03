<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchaseReturnPaymentController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'purchases-return'], function () {

    Route::get('/', [PurchaseReturnController::class, 'index']);

    Route::get('/trashed', [PurchaseReturnController::class, 'trashed']);

    Route::get('/{id}', [PurchaseReturnController::class, 'show']);

    Route::post('/create', [PurchaseReturnController::class, 'create']);

    Route::get('/{id}/edit', [PurchaseReturnController::class, 'edit']);

    Route::put('/{id}', [PurchaseReturnController::class, 'update']);

    Route::post('/{id}/trash', [PurchaseReturnController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [PurchaseReturnController::class, 'restore']);

    Route::post('/{id}', [PurchaseReturnController::class, 'remove']);
});

############################################# Purchase Return Payment #############################################

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'purchases-return/{purchaseId}/payments'], function () {

    Route::get('/', [PurchaseReturnPaymentController::class, 'index']);

    Route::get('/{id}', [PurchaseReturnPaymentController::class, 'show']);

    Route::post('/create', [PurchaseReturnPaymentController::class, 'create']);

    Route::put('/{id}', [PurchaseReturnPaymentController::class, 'update']);

    Route::post('/{id}', [PurchaseReturnPaymentController::class, 'remove']);
});
