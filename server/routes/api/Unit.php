<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'unit'], function () {

    Route::get('/', [UnitController::class, 'index']);

    Route::get('/options', [UnitController::class, 'options']);

    Route::post('/create', [UnitController::class, 'create']);

    Route::put('/{id}', [UnitController::class, 'update']);

    Route::post('/{id}', [UnitController::class, 'remove']);
});
