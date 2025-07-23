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
Schedule::command('db:backup mysql')->dailyAt('00:00');
Schedule::command('db:backup mongodb')->dailyAt('00:30');
Schedule::command('document-file:delete')->dailyAt('01:00');