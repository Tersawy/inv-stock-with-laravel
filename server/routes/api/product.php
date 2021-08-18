<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'product'], function () {
    Route::get('/', [ProductController::class, 'index']);

    Route::get('/options', [ProductController::class, 'options']);

    Route::post('/details', [ProductController::class, 'details']);

    Route::get('/{id}', [ProductController::class, 'show']);

    Route::put('/{id}', [ProductController::class, 'update']);

    Route::post('/create', [ProductController::class, 'create']);

    Route::post('/{id}', [ProductController::class, 'remove']);
});

############################################ Product Variant ############################################

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'product/{productId}/variant'], function () {

    Route::get('/', [ProductVariantController::class, 'index']);

    Route::get('/{id}', [ProductVariantController::class, 'show']);

    Route::put('/{id}', [ProductVariantController::class, 'update']);

    Route::post('/create', [ProductVariantController::class, 'create']);

    Route::post('/{id}', [ProductVariantController::class, 'remove']);
});
