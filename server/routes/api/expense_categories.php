<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseCategoryController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'expense-categories'], function () {

    Route::get('/', [ExpenseCategoryController::class, 'index']);

    Route::get('/options', [ExpenseCategoryController::class, 'options']);

    Route::put('/{id}', [ExpenseCategoryController::class, 'update']);

    Route::post('/create', [ExpenseCategoryController::class, 'create']);

    Route::post('/{id}', [ExpenseCategoryController::class, 'remove']);
});
