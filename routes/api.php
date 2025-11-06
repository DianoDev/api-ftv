<?php

use App\Http\Controllers\Users\UsersController;
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
Route::group(['prefix' => 'register'], function () {
    Route::get('/', [UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/list', [UsersController::class, 'list'])->name('admin.users.list');
    Route::get('/{id}', [UsersController::class, 'edit'])->name('admin.users.edit');
    Route::post('/', [UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/{id}', [UsersController::class, 'update'])->name('admin.users.update');
    Route::delete('/{id}', [UsersController::class, 'delete'])->name('admin.users.delete');
});
