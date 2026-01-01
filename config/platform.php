<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sub-Domain Routing
    |--------------------------------------------------------------------------
    |
    | Define o domínio associado à aplicação administrativa. Utilizado para
    | restringir o registro de rotas internas do dashboard em subdomínios que
    | não necessitem de acesso ao painel (ex: 'admin.example.com').
    |
    */
    'domain' => env('PLATFORM_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Route Prefixes
    |--------------------------------------------------------------------------
    |
    | Prefixo aplicado a todas as rotas do painel administrativo.
    | Exemplos comuns: '/', '/admin' ou '/panel'.
    |
    */
    'prefix' => env('PLATFORM_PREFIX', '/admin'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middlewares aplicados a todas as rotas do dashboard Orchid.
    | 'public'  – Rotas acessíveis sem autenticação.
    | 'private' – Rotas protegidas (requer autenticação via 'platform').
    |
    */
    'middleware' => [
        'public'  => ['web', 'cache.headers:private;must_revalidate;etag'],
        'private' => ['web', 'platform', 'cache.headers:private;must_revalidate;etag'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    |
    | Nome do guard de autenticação utilizado nas rotas administrativas.
    | Permite integração com setups multi-auth do Laravel.
    |
    */
    'guard' => env('AUTH_GUARD', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Page
    |--------------------------------------------------------------------------
    |
    | Controla a exibição das páginas de autenticação nativas do Orchid
    | (login, recuperação de senha, etc.). Defina como false caso utilize
    | soluções externas (ex: Laravel Jetstream ou Fortify).
    |
    */
    'auth' => true,

    /*
    |--------------------------------------------------------------------------
    | Main Route
    |--------------------------------------------------------------------------
    |
    | Rota principal do dashboard. Usuários são redirecionados para esta
    | página ao acessar o painel ou clicar no logo.
    |
    */
    'index' => 'platform.main',

    /*
    |--------------------------------------------------------------------------
    | User Profile Route
    |--------------------------------------------------------------------------
    |
    | Rota para acesso ao perfil do usuário autenticado.
    |
    */
    'profile' => 'platform.profile',

    /*
    |--------------------------------------------------------------------------
    | Dashboard Resource
    |--------------------------------------------------------------------------
    |
    | Links para estilos e scripts carregados automaticamente no dashboard.
    | Aceita caminhos locais ou URLs externas.
    |
    */
    'resource' => [
        'stylesheets' => [],
        'scripts'     => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vite Resource
    |--------------------------------------------------------------------------
    |
    | Arquivos de entrada para processamento pelo Vite (JS/CSS).
    | Exemplo: ['resources/css/app.css', 'resources/js/app.js']
    |
    */
    'vite' => [],

    /*
    |--------------------------------------------------------------------------
    | Template View
    |--------------------------------------------------------------------------
    |
    | Templates personalizados para header e footer do dashboard.
    | Use notação Blade (ex: 'brand.header' para resources/views/brand/header.blade.php).
    |
    */
    'template' => [
        'header' => '',
        'footer' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Attachment Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações padrão para anexos de arquivos.
    | 'disk'      – Disco de armazenamento (padrão: 'public').
    | 'generator' – Classe responsável pela geração de nomes únicos.
    |
    */
    'attachment' => [
        'disk'      => env('PLATFORM_FILESYSTEM_DISK', 'public'),
        'generator' => \Orchid\Attachment\Engines\Generator::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Icons Path
    |--------------------------------------------------------------------------
    |
    | Caminhos para diretórios de ícones SVG utilizados no dashboard.
    |
    */
    'icons' => [
        'bs' => \Orchid\Support\BootstrapIconsPath::getFolder(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configuração do sistema de notificações do Orchid.
    | 'enabled'  – Ativa/desativa o ícone de sino e polling AJAX.
    | 'interval' – Intervalo de atualização em segundos (padrão: 60).
    |
    */
    'notifications' => [
        'enabled'  => false, // Desativado para evitar lentidão no endpoint /admin/api/notifications
        'interval' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    |
    | Modelos habilitados para busca global na sidebar.
    | Requer implementação de Presenter e Scout no modelo.
    |
    */
    'search' => [
        // \App\Models\User::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Hotwire Turbo
    |--------------------------------------------------------------------------
    |
    | Configurações do Turbo Drive (cache de páginas visitadas e prefetch).
    |
    */
    'turbo' => [
        'cache'          => true,
        'prefetch'       => true,
        'refresh-method' => 'replace',
        'refresh-scroll' => 'preserve',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Page
    |--------------------------------------------------------------------------
    |
    | Habilita a página 404 personalizada do Orchid para rotas não encontradas
    | no domínio/prefixo configurado.
    |
    */
    'fallback' => true,

    /*
    |--------------------------------------------------------------------------
    | Workspace
    |--------------------------------------------------------------------------
    |
    | Template de layout para telas do dashboard.
    | 'compact' – Largura fixa; 'full' – Largura total.
    |
    */
    'workspace' => 'platform::workspace.compact',

    /*
    |--------------------------------------------------------------------------
    | Prevents Abandonment
    |--------------------------------------------------------------------------
    |
    | Ativa recurso de prevenção de abandono de formulários não salvos.
    |
    */
    'prevents_abandonment' => true,

    /*
    |--------------------------------------------------------------------------
    | Service Provider
    |--------------------------------------------------------------------------
    |
    | Namespace do service provider personalizado do Orchid.
    |
    */
    'provider' => \App\Orchid\PlatformProvider::class,
];