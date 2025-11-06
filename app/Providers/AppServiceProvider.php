<?php

namespace App\Providers;

use App\Databases\Contracts\AuthContract;
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
        app()->bind(RegisterContract::class, RegisterRepository::class);
        app()->bind(AuthContract::class, AuthRepository::class);
    }
}
