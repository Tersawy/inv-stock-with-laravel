<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'brands'], function () {
    Route::get('/', [BrandController::class, 'index']);

    Route::get('/options', [BrandController::class, 'options']);

    Route::get('/{id}', [BrandController::class, 'show']);

    Route::put('/{id}', [BrandController::class, 'update']);

    Route::post('/create', [BrandController::class, 'create']);

    Route::post('/{id}', [BrandController::class, 'remove']);
});
