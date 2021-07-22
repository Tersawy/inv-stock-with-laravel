<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'expense'], function () {

    Route::get('/', [ExpenseController::class, 'index']);

    Route::get('/options', [ExpenseController::class, 'options']);

    Route::get('/trashed', [ExpenseController::class, 'trashed']);

    Route::get('/{id}', [ExpenseController::class, 'show']);

    Route::put('/{id}', [ExpenseController::class, 'update']);

    Route::post('/create', [ExpenseController::class, 'create']);

    Route::post('/{id}/trash', [ExpenseController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [ExpenseController::class, 'restore']);

    Route::post('/{id}', [ExpenseController::class, 'remove']);
});
