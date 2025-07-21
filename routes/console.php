<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('task:init')->everyMinute();
Schedule::command('db:backup mysql')->dailyAt('03:00');
Schedule::command('document-file:delete')->dailyAt('06:00');