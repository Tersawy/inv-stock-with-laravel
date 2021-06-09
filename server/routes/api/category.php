<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, 'index']);

    Route::get('/options', [CategoryController::class, 'options']);

    Route::get('/trashed', [CategoryController::class, 'trashed']);

    Route::get('/{id}', [CategoryController::class, 'show']);

    Route::put('/{id}', [CategoryController::class, 'update']);

    Route::post('/create', [CategoryController::class, 'create']);

    Route::post('/{id}/trash', [CategoryController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [CategoryController::class, 'restore']);

    Route::post('/{id}', [CategoryController::class, 'remove']);
});
