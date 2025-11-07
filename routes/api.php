<?php

use App\Http\Controllers\Admin\ArenasController;
use App\Http\Controllers\Admin\AulasController;
use App\Http\Controllers\Admin\CampeonatosController;
use App\Http\Controllers\Admin\JogadoresController;
use App\Http\Controllers\Admin\ProfessoresController;
use App\Http\Controllers\Admin\QuadrasController;
use App\Http\Controllers\Admin\RachasController;
use App\Http\Controllers\Admin\SolicitacoesRachaController;
use App\Http\Controllers\Users\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\AuthController;

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
    Route::get('/', [RegisterController::class, 'index'])->name('users.index');
    Route::get('/list', [RegisterController::class, 'list'])->name('users.list');
    Route::get('/{id}', [RegisterController::class, 'edit'])->name('users.edit');
    Route::post('/', [RegisterController::class, 'create'])->name('users.create');
    Route::post('/{id}', [RegisterController::class, 'update'])->name('users.update');
    Route::delete('/{id}', [RegisterController::class, 'delete'])->name('users.delete');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'Aulas'], function () {
        Route::get('/', [AulasController::class, 'index'])->name('aulas.index');
        Route::get('/list', [AulasController::class, 'list'])->name('aulas.list');
        Route::get('/{id}', [AulasController::class, 'edit'])->name('aulas.edit');
        Route::post('/', [AulasController::class, 'create'])->name('aulas.create');
        Route::post('/{id}', [AulasController::class, 'update'])->name('aulas.update');
        Route::delete('/{id}', [AulasController::class, 'delete'])->name('aulas.delete');
    });
    Route::group(['prefix' => 'Arenas'], function () {
        Route::get('/', [ArenasController::class, 'index'])->name('Arenas.index');
        Route::get('/list', [ArenasController::class, 'list'])->name('Arenas.list');
        Route::get('/{id}', [ArenasController::class, 'edit'])->name('Arenas.edit');
        Route::post('/', [ArenasController::class, 'create'])->name('Arenas.create');
        Route::post('/{id}', [ArenasController::class, 'update'])->name('Arenas.update');
        Route::delete('/{id}', [ArenasController::class, 'delete'])->name('Arenas.delete');
    });

    Route::group(['prefix' => 'campeonatos'], function () {
        Route::get('/', [CampeonatosController::class, 'index'])->name('campeonatos.index');
        Route::get('/list', [CampeonatosController::class, 'list'])->name('campeonatos.list');
        Route::get('/{id}', [CampeonatosController::class, 'edit'])->name('campeonatos.edit');
        Route::post('/', [CampeonatosController::class, 'create'])->name('campeonatos.create');
        Route::post('/{id}', [CampeonatosController::class, 'update'])->name('campeonatos.update');
        Route::delete('/{id}', [CampeonatosController::class, 'delete'])->name('campeonatos.delete');
    });
    Route::group(['prefix' => 'jogadores'], function () {
        Route::get('/', [JogadoresController::class, 'index'])->name('jogadores.index');
        Route::get('/list', [JogadoresController::class, 'list'])->name('jogadores.list');
        Route::get('/{id}', [JogadoresController::class, 'edit'])->name('jogadores.edit');
        Route::post('/', [JogadoresController::class, 'create'])->name('jogadores.create');
        Route::post('/{id}', [JogadoresController::class, 'update'])->name('jogadores.update');
        Route::delete('/{id}', [JogadoresController::class, 'delete'])->name('jogadores.delete');
    });
    Route::group(['prefix' => 'professores'], function () {
        Route::get('/', [ProfessoresController::class, 'index'])->name('professores.index');
        Route::get('/list', [ProfessoresController::class, 'list'])->name('professores.list');
        Route::get('/{id}', [ProfessoresController::class, 'edit'])->name('professores.edit');
        Route::post('/', [ProfessoresController::class, 'create'])->name('professores.create');
        Route::post('/{id}', [ProfessoresController::class, 'update'])->name('professores.update');
        Route::delete('/{id}', [ProfessoresController::class, 'delete'])->name('professores.delete');
    });
    Route::group(['prefix' => 'quadras'], function () {
        Route::get('/', [QuadrasController::class, 'index'])->name('quadras.index');
        Route::get('/list', [QuadrasController::class, 'list'])->name('quadras.list');
        Route::get('/{id}', [QuadrasController::class, 'edit'])->name('quadras.edit');
        Route::post('/', [QuadrasController::class, 'create'])->name('quadras.create');
        Route::post('/{id}', [QuadrasController::class, 'update'])->name('quadras.update');
        Route::delete('/{id}', [QuadrasController::class, 'delete'])->name('quadras.delete');
    });
    Route::group(['prefix' => 'rachas'], function () {
        Route::get('/', [RachasController::class, 'index'])->name('rachas.index');
        Route::get('/list', [RachasController::class, 'list'])->name('rachas.list');
        Route::get('/{id}', [RachasController::class, 'edit'])->name('rachas.edit');
        Route::post('/', [RachasController::class, 'create'])->name('rachas.create');
        Route::post('/{id}', [RachasController::class, 'update'])->name('rachas.update');
        Route::delete('/{id}', [RachasController::class, 'delete'])->name('rachas.delete');
    });
    Route::group(['prefix' => 'solicitacoes-racha'], function () {
        Route::get('/', [SolicitacoesRachaController::class, 'index'])->name('solicitacoes_racha.index');
        Route::get('/list', [SolicitacoesRachaController::class, 'list'])->name('solicitacoes_racha.list');
        Route::get('/{id}', [SolicitacoesRachaController::class, 'edit'])->name('solicitacoes_racha.edit');
        Route::post('/', [SolicitacoesRachaController::class, 'create'])->name('solicitacoes_racha.create');
        Route::post('/{id}', [SolicitacoesRachaController::class, 'update'])->name('solicitacoes_racha.update');
        Route::delete('/{id}', [SolicitacoesRachaController::class, 'delete'])->name('solicitacoes_racha.delete');
    });
});
