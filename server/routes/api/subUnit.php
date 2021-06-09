<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubUnitController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'sub-unit'], function () {

    Route::post('/create', [SubUnitController::class, 'create']);

    Route::put('/{id}', [SubUnitController::class, 'update']);

    Route::post('/{id}', [SubUnitController::class, 'remove']);
});
