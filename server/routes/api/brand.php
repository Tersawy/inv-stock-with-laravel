<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'brand'], function () {
    Route::get('/', [BrandController::class, 'index']);

    Route::get('/options', [BrandController::class, 'options']);

    Route::get('/trashed', [BrandController::class, 'trashed']);

    Route::get('/{id}', [BrandController::class, 'show']);

    Route::put('/{id}', [BrandController::class, 'update']);

    Route::post('/create', [BrandController::class, 'create']);

    Route::post('/{id}/trash', [BrandController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [BrandController::class, 'restore']);

    Route::post('/{id}', [BrandController::class, 'remove']);
});
