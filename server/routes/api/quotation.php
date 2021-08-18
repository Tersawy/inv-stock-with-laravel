<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'quotation'], function () {

    Route::get('/', [QuotationController::class, 'index']);

    Route::get('/trashed', [QuotationController::class, 'trashed']);

    Route::get('/{id}', [QuotationController::class, 'show']);

    Route::post('/create', [QuotationController::class, 'create']);

    Route::get('/{id}/edit', [QuotationController::class, 'edit']);

    Route::put('/{id}', [QuotationController::class, 'update']);

    Route::post('/{id}/trash', [QuotationController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [QuotationController::class, 'restore']);

    Route::post('/{id}', [QuotationController::class, 'remove']);
});
