<?php

namespace App\Providers;

use App\Support\InstitutionalInfo;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

        // Dados institucionais do painel: uma carga por request, compartilhada por layout, rodapé e JSON-LD.
        $this->app->scoped(InstitutionalInfo::class, fn () => InstitutionalInfo::load());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer(['layouts.app', 'components.footer'], function ($view): void {
            $view->with('institutional', $this->app->make(InstitutionalInfo::class));
        });

        // Sem isto, um worker de longa duração (ou várias requests no mesmo teste) reaproveitaria os dados antigos.
        Event::listen(RequestHandled::class, fn () => $this->app->forgetInstance(InstitutionalInfo::class));

        // O schema pertence ao painel; o espelho só é migrado em desenvolvimento e nos testes.
        if ($this->app->environment(['local', 'testing'])) {
            $this->loadMigrationsFrom(database_path('migrations/testing'));
        }
    }
}
