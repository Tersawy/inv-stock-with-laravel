<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'currency'], function () {

    Route::get('/', [CurrencyController::class, 'index']);

    Route::get('/trashed', [CurrencyController::class, 'trashed']);

    Route::get('/{id}', [CurrencyController::class, 'show']);

    Route::post('/create', [CurrencyController::class, 'create']);

    Route::put('/{id}', [CurrencyController::class, 'update']);

    Route::post('/{id}/trash', [CurrencyController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [CurrencyController::class, 'restore']);

    Route::post('/{id}', [CurrencyController::class, 'remove']);
});
