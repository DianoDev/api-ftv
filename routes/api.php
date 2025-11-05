<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::get('/teste', [AuthController::class, 'teste']);
// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::get('/teste', [AuthController::class, 'teste']);
    Route::post('/register/jogador', [AuthController::class, 'registerJogador']);
    Route::post('/register/professor', [AuthController::class, 'registerProfessor']);
    Route::post('/register/arena', [AuthController::class, 'registerArena']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas protegidas (requer autenticação)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
