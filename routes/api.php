<?php

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\LotacaoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;

Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('renova-token', [LoginController::class, 'renovaToken']);

    Route::apiResource('servidor-efetivo', ServidorEfetivoController::class);

    Route::apiResource('servidor-temporario', ServidorTemporarioController::class);

    Route::apiResource('unidade', UnidadeController::class);

    Route::apiResource('lotacao', LotacaoController::class);

});
