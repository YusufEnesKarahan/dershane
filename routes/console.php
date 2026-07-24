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

Schedule::call(fn () => app(\App\Domain\System\Services\AutomationService::class)->paymentReminders())->name('automation:payment-reminders')->dailyAt('08:00')->withoutOverlapping();
Schedule::call(fn () => app(\App\Domain\System\Services\AutomationService::class)->upcomingExams())->name('automation:upcoming-exams')->dailyAt('08:15')->withoutOverlapping();
Schedule::call(fn () => app(\App\Domain\System\Services\AutomationService::class)->attendanceWarnings())->name('automation:attendance-warnings')->dailyAt('08:30')->withoutOverlapping();
Schedule::call(fn () => app(\App\Domain\System\Services\AutomationService::class)->pendingFollowups())->name('automation:pending-followups')->dailyAt('08:45')->withoutOverlapping();
Schedule::call(fn () => app(\App\Domain\System\Services\AutomationService::class)->weeklySystemReport())->name('automation:weekly-system-report')->weeklyOn(1, '07:00')->withoutOverlapping();
Schedule::call(fn () => app(\App\Domain\System\Services\AutomationService::class)->weeklyCleanup())->name('automation:weekly-cleanup')->weeklyOn(1, '07:30')->withoutOverlapping();
