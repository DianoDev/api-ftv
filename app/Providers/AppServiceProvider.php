<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Databases\Contracts\UsersContract;
use App\Databases\Repositories\UsersRepository;

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
        app()->bind(UsersContract::class, UsersRepository::class);
        //
    }
}
