<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('user', UserController::class)->except(['store', 'destroy']);
    Route::post('export-user', [UserController::class, 'exportUser']);
});