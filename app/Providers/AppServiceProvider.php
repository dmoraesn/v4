<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra serviços da aplicação.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa serviços da aplicação.
     *
     * Registra o middleware ContarAcesso no grupo 'web' para contagem global de acessos.
     */
    public function boot(): void
    {
        // Adiciona o middleware ao grupo web (executa em todas as rotas públicas)
        Route::pushMiddlewareToGroup('web', \App\Http\Middleware\ContarAcesso::class);
    }
}