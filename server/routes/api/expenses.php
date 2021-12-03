<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'expenses'], function () {

    Route::get('/', [ExpenseController::class, 'index']);

    Route::get('/options', [ExpenseController::class, 'options']);

    Route::put('/{id}', [ExpenseController::class, 'update']);

    Route::post('/create', [ExpenseController::class, 'create']);

    Route::post('/{id}', [ExpenseController::class, 'remove']);
});
