<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SaleReturnPaymentController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'sales-return'], function () {

    Route::get('/', [SaleReturnController::class, 'index']);

    Route::get('/trashed', [SaleReturnController::class, 'trashed']);

    Route::get('/{id}', [SaleReturnController::class, 'show']);

    Route::post('/create', [SaleReturnController::class, 'create']);

    Route::get('/{id}/edit', [SaleReturnController::class, 'edit']);

    Route::put('/{id}', [SaleReturnController::class, 'update']);

    Route::post('/{id}/trash', [SaleReturnController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [SaleReturnController::class, 'restore']);

    Route::post('/{id}', [SaleReturnController::class, 'remove']);
});

############################################# Sale Return Payment #############################################

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'sales-return/{saleId}/payments'], function () {

    Route::get('/', [SaleReturnPaymentController::class, 'index']);

    Route::get('/{id}', [SaleReturnPaymentController::class, 'show']);

    Route::post('/create', [SaleReturnPaymentController::class, 'create']);

    Route::put('/{id}', [SaleReturnPaymentController::class, 'update']);

    Route::post('/{id}', [SaleReturnPaymentController::class, 'remove']);
});
