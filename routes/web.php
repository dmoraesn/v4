<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\CidadePublicController;
use App\Http\Controllers\ParlamentarController;

/*
|--------------------------------------------------------------------------
| HOME GLOBAL
|--------------------------------------------------------------------------
| Página inicial do BuscaLeis
| - Busca global
| - Overview do sistema
| - Lista de cidades
|
| Ex: /
*/
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

/*
|--------------------------------------------------------------------------
| BUSCA GLOBAL (INTELIGENTE)
|--------------------------------------------------------------------------
| Endpoint único de busca
| Estratégia:
| - Se o termo corresponder a uma cidade → redireciona
| - Caso contrário → mantém contexto global (futuro)
|
| Ex:
| /buscar?q=educação
| /buscar?q=educação&cidade=fortaleza
*/
Route::get('/buscar', [HomeController::class, 'buscar'])
    ->name('buscar');

/*
|--------------------------------------------------------------------------
| LISTAGEM PÚBLICA DE CIDADES
|--------------------------------------------------------------------------
| Página dedicada à listagem de todas as cidades indexadas
| URL pública: /cidades
|
| Resolve o erro 404 ao acessar esta rota
*/
Route::get('/cidades', [CidadePublicController::class, 'index'])
    ->name('cidades.index');

/*
|--------------------------------------------------------------------------
| ROTAS POR CIDADE
|--------------------------------------------------------------------------
| Todas as rotas SEMPRE preservam o slug da cidade
| O slug é a chave principal de contexto do sistema
|
| Ex:
| /fortaleza
| /sao-goncalo-do-amarante
*/
Route::prefix('{cidade}')
    ->where(['cidade' => '[a-z0-9\-]+'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | HOME DA CIDADE
        |--------------------------------------------------------------------------
        | Dashboard legislativo da cidade
        |
        | Ex: /fortaleza
        */
        Route::get('/', [CidadeController::class, 'show'])
            ->name('cidade.home');

        /*
        |--------------------------------------------------------------------------
        | MATÉRIAS DA CIDADE
        |--------------------------------------------------------------------------
        | Lista paginada de matérias (SAPL)
        | Suporta busca por palavra-chave e autor
        |
        | Ex:
        | /fortaleza/materias
        | /fortaleza/materias?q=educação
        */
        Route::get('/materias', [MateriaController::class, 'index'])
            ->name('materias.index');

        /*
        |--------------------------------------------------------------------------
        | DETALHE DA MATÉRIA
        |--------------------------------------------------------------------------
        | Página individual da matéria
        |
        | Ex: /fortaleza/materias/12345
        */
        Route::get('/materias/{id}', [MateriaController::class, 'show'])
            ->whereNumber('id')
            ->name('materias.show');

        /*
        |--------------------------------------------------------------------------
        | AUTORES / PARLAMENTARES
        |--------------------------------------------------------------------------
        | Lista simples de vereadores/autores da cidade
        |
        | Ex: /fortaleza/autores
        */
        Route::get('/autores', [AutorController::class, 'index'])
            ->name('cidade.autores');

        /*
        |--------------------------------------------------------------------------
        | PERFIL INDIVIDUAL DO PARLAMENTAR
        |--------------------------------------------------------------------------
        | Exemplo: /sao-goncalo-do-amarante/parlamentar/joao-da-silva
        */
        Route::get('/parlamentar/{parlamentar}', [ParlamentarController::class, 'show'])
            ->where(['parlamentar' => '[a-z0-9\-]+'])
            ->name('parlamentar.show');
    });