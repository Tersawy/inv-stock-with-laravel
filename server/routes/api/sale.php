<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalePaymentController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'sale'], function () {

    Route::get('/', [SaleController::class, 'index']);

    Route::get('/trashed', [SaleController::class, 'trashed']);

    Route::get('/{id}', [SaleController::class, 'show']);

    Route::post('/create', [SaleController::class, 'create']);

    Route::put('/{id}', [SaleController::class, 'update']);

    Route::post('/{id}/trash', [SaleController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [SaleController::class, 'restore']);

    Route::post('/{id}', [SaleController::class, 'remove']);
});

############################################# Sale Payment #############################################

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'sale/{saleId}/payments'], function () {

    Route::get('/', [SalePaymentController::class, 'index']);

    Route::get('/{id}', [SalePaymentController::class, 'show']);

    Route::post('/create', [SalePaymentController::class, 'create']);

    Route::put('/{id}', [SalePaymentController::class, 'update']);

    Route::post('/{id}', [SalePaymentController::class, 'remove']);
});
