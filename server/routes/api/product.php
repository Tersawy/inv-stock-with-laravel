<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'product'], function () {
    Route::get('/', [ProductController::class, 'index']);

    Route::get('/options', [ProductController::class, 'options']);

    Route::get('/{id}', [ProductController::class, 'show']);

    Route::put('/{id}', [ProductController::class, 'update']);

    Route::post('/create', [ProductController::class, 'create']);

    Route::post('/{id}/trash', [ProductController::class, 'moveToTrash']);

    Route::get('/trashed', [ProductController::class, 'trashed']);

    Route::post('/{id}/restore', [ProductController::class, 'restore']);

    Route::post('/{id}', [ProductController::class, 'remove']);
});
