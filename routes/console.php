<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Comandos Artisan
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler (Laravel 12)
|--------------------------------------------------------------------------
| Aqui substitui totalmente o antigo App\Console\Kernel
*/

Schedule::command('buscaleis:update-stats')
    ->hourly()               // roda a cada hora
    ->withoutOverlapping()   // evita concorrência
    ->runInBackground();     // não bloqueia o scheduler
