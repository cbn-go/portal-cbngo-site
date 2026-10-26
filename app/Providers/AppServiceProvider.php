<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // O schema pertence ao painel; o espelho só é migrado em desenvolvimento e nos testes.
        if ($this->app->environment(['local', 'testing'])) {
            $this->loadMigrationsFrom(database_path('migrations/testing'));
        }
    }
}
