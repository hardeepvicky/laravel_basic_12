<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Schedule;
>>>>>>> base_v3

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
<<<<<<< HEAD
=======


Schedule::command('task:init')->everyMinute();
Schedule::command('lead:snooze')->everyMinute();

Schedule::command('log:delete')->dailyAt('00:00');
Schedule::command('document-file:delete')->dailyAt('00:00');

Schedule::command('db:backup mysql')->dailyAt('01:00');
Schedule::command('db:backup mongodb')->dailyAt('01:30');
>>>>>>> base_v3
