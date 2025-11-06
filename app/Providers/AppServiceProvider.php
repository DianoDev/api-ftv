<?php

namespace App\Providers;

use App\Databases\Contracts\AuthContract;
use App\Databases\Contracts\SolicitacoesRachaContract;
use App\Databases\Repositories\SolicitacoesRachaRepository;
use App\Databases\Contracts\RachasContract;
use App\Databases\Repositories\RachasRepository;
use App\Databases\Contracts\QuadrasContract;
use App\Databases\Repositories\QuadrasRepository;
use App\Databases\Contracts\ProfessoresContract;
use App\Databases\Repositories\ProfessoresRepository;
use App\Databases\Contracts\JogadoresContract;
use App\Databases\Repositories\JogadoresRepository;
use App\Databases\Contracts\CampeonatosContract;
use App\Databases\Repositories\CampeonatosRepository;
use App\Databases\Contracts\ArenasContract;
use App\Databases\Repositories\ArenasRepository;
use App\Databases\Contracts\AulasContract;
use App\Databases\Repositories\AulasRepository;
use App\Databases\Repositories\AuthRepository;
use Illuminate\Support\ServiceProvider;
use App\Databases\Contracts\RegisterContract;
use App\Databases\Repositories\RegisterRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app()->bind(SolicitacoesRachaContract::class, SolicitacoesRachaRepository::class);
        app()->bind(RachasContract::class, RachasRepository::class);
        app()->bind(QuadrasContract::class, QuadrasRepository::class);
        app()->bind(ProfessoresContract::class, ProfessoresRepository::class);
        app()->bind(JogadoresContract::class, JogadoresRepository::class);
        app()->bind(CampeonatosContract::class, CampeonatosRepository::class);
        app()->bind(ArenasContract::class, ArenasRepository::class);
        app()->bind(AulasContract::class, AulasRepository::class);
        app()->bind(RegisterContract::class, RegisterRepository::class);
        app()->bind(AuthContract::class, AuthRepository::class);
    }
}
