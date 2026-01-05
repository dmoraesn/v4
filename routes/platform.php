<?php

declare(strict_types=1);

use App\Models\Parlamentar;
use App\Models\Partido;
use App\Orchid\Screens\CidadeEditScreen;
use App\Orchid\Screens\CidadeListScreen;
use App\Orchid\Screens\ParlamentarByCityListScreen;
use App\Orchid\Screens\ParlamentarDashboardScreen;
use App\Orchid\Screens\ParlamentarEditScreen;
use App\Orchid\Screens\ParlamentarListScreen;
use App\Orchid\Screens\PartidoEditScreen;
use App\Orchid\Screens\PartidoListScreen;
use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Aqui são registradas as rotas do painel administrativo Orchid.
|
*/

Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Perfil do usuário
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Usuários do sistema
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Papéis (Roles)
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Cidades Indexadas
Route::screen('cidades', CidadeListScreen::class)
    ->name('platform.cidade.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Cidades Indexadas', route('platform.cidade.list')));

Route::screen('cidades/create', CidadeEditScreen::class)
    ->name('platform.cidade.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.cidade.list')
        ->push('Criar Nova Cidade', route('platform.cidade.create')));

Route::screen('cidades/{cidade}/edit', CidadeEditScreen::class)
    ->name('platform.cidade.edit')
    ->breadcrumbs(fn (Trail $trail, $cidade) => $trail
        ->parent('platform.cidade.list')
        ->push("Editar {$cidade->nome}", route('platform.cidade.edit', $cidade)));

// Parlamentares - Nova estrutura com dashboard
Route::screen('parlamentar/dashboard', ParlamentarDashboardScreen::class)
    ->name('platform.parlamentar.dashboard')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Parlamentares', route('platform.parlamentar.dashboard')));

Route::screen('parlamentar/cidade/{cidade}', ParlamentarByCityListScreen::class)
    ->name('platform.parlamentar.list.by.city')
    ->breadcrumbs(fn (Trail $trail, $cidade) => $trail
        ->parent('platform.parlamentar.dashboard')
        ->push("Parlamentares - {$cidade->nome} / {$cidade->uf}", route('platform.parlamentar.list.by.city', $cidade)));

// Rotas de criação e edição de parlamentares (mantidas para funcionalidade completa)
Route::screen('parlamentares/create', ParlamentarEditScreen::class)
    ->name('platform.parlamentar.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.parlamentar.dashboard')
        ->push('Criar Parlamentar', route('platform.parlamentar.create')));

Route::screen('parlamentares/{parlamentar}/edit', ParlamentarEditScreen::class)
    ->name('platform.parlamentar.edit')
    ->breadcrumbs(fn (Trail $trail, Parlamentar $parlamentar) => $trail
        ->parent('platform.parlamentar.dashboard')
        ->push("Editar {$parlamentar->nome_parlamentar}", route('platform.parlamentar.edit', $parlamentar)));

// Partidos Políticos
Route::screen('partidos', PartidoListScreen::class)
    ->name('platform.partido.list')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Partidos', route('platform.partido.list')));

Route::screen('partidos/create', PartidoEditScreen::class)
    ->name('platform.partido.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.partido.list')
        ->push('Criar Partido', route('platform.partido.create')));

Route::screen('partidos/{partido}/edit', PartidoEditScreen::class)
    ->name('platform.partido.edit')
    ->breadcrumbs(fn (Trail $trail, Partido $partido) => $trail
        ->parent('platform.partido.list')
        ->push("Editar {$partido->nome}", route('platform.partido.edit', $partido)));