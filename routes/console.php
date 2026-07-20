<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| HQ Panel Synchronization Schedule
|--------------------------------------------------------------------------
|
| Schedules periodic heartbeat, telemetry ingestion, and pending command
| polling with HQ Panel every 15 minutes.
|
*/
Schedule::command('hq:sync')->everyFifteenMinutes();
