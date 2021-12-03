<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdjustmentController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'adjustments'], function () {

    Route::get('/', [AdjustmentController::class, 'index']);

    Route::get('/trashed', [AdjustmentController::class, 'trashed']);

    Route::get('/{id}', [AdjustmentController::class, 'show']);

    Route::post('/create', [AdjustmentController::class, 'create']);

    Route::get('/{id}/edit', [AdjustmentController::class, 'edit']);

    Route::put('/{id}', [AdjustmentController::class, 'update']);

    Route::post('/{id}/trash', [AdjustmentController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [AdjustmentController::class, 'restore']);

    Route::post('/{id}', [AdjustmentController::class, 'remove']);
});
