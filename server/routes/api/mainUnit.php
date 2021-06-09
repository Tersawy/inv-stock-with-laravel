<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainUnitController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'main-unit'], function () {

    Route::get('/', [MainUnitController::class, 'index']);

    Route::get('/options', [MainUnitController::class, 'options']);

    Route::post('/create', [MainUnitController::class, 'create']);

    Route::put('/{id}', [MainUnitController::class, 'update']);

    Route::post('/{id}', [MainUnitController::class, 'remove']);
});
