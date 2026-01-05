<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    /**
     * Registra os itens do menu lateral do painel Orchid.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            // Grupo principal: Inteligência Legislativa (BI)
            Menu::make('Inteligência Legislativa (BI)')
                ->icon('bs.graph-up-arrow')
                ->title('Inteligência Legislativa (BI)')
                ->route('platform.cidade.list'),

            Menu::make('Cidades Indexadas')
                ->icon('bs.building')
                ->route('platform.cidade.list'),

            Menu::make('Parlamentares')
                ->icon('bs.people')
                ->route('platform.parlamentar.dashboard'), // Alterado para o novo dashboard com cards por cidade

            Menu::make('Partidos')
                ->icon('bs.flag')
                ->route('platform.partido.list')
                ->divider(),

            // Grupo: Configurações
            Menu::make(__('Users'))
                ->icon('bs.person')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Configurações')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles'),

            // Documentação
            Menu::make('Documentation')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),

            Menu::make('Changelog')
                ->icon('bs.box-arrow-up-right')
                ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }

    /**
     * Registra as permissões do sistema.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}