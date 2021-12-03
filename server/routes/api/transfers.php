<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'transfers'], function () {

    Route::get('/', [TransferController::class, 'index']);

    Route::get('/trashed', [TransferController::class, 'trashed']);

    Route::get('/{id}', [TransferController::class, 'show']);

    Route::post('/create', [TransferController::class, 'create']);

    Route::get('/{id}/edit', [TransferController::class, 'edit']);

    Route::put('/{id}', [TransferController::class, 'update']);

    Route::post('/{id}/trash', [TransferController::class, 'moveToTrash']);

    Route::post('/{id}/restore', [TransferController::class, 'restore']);

    Route::post('/{id}', [TransferController::class, 'remove']);
});
