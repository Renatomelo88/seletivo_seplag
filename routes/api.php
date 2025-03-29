<?php

use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServidorEfetivoApiController;

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/renova-token', [LoginController::class, 'renovaToken']);

    Route::apiResource('/servidor-efetivo', ServidorEfetivoApiController::class);


});
