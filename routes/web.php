<?php

use App\Http\Controllers\Admin\CategoriaCampeonatoController;
use App\Http\Controllers\Admin\SolicitacoesRachaController;
use App\Http\Controllers\Admin\RachasController;
use App\Http\Controllers\Admin\QuadrasController;
use App\Http\Controllers\Admin\ProfessoresController;
use App\Http\Controllers\Admin\JogadoresController;
use App\Http\Controllers\Admin\CampeonatosController;
use App\Http\Controllers\Admin\ArenasController;
use App\Http\Controllers\Admin\AulasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
