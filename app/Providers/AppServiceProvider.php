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
        // Na Hostinger o document root é public_html (o deploy copia public/ para lá). Sem isto o Laravel
        // leria o manifesto do Vite de uma pasta public/ antiga e apontaria para CSS/JS que não existem mais.
        if (is_dir($this->app->basePath('public_html'))) {
            $this->app->usePublicPath($this->app->basePath('public_html'));
        }
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
