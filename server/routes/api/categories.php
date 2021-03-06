<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);

    Route::get('/options', [CategoryController::class, 'options']);

    Route::put('/{id}', [CategoryController::class, 'update']);

    Route::post('/create', [CategoryController::class, 'create']);

    Route::post('/{id}', [CategoryController::class, 'remove']);
});
