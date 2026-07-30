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

Schedule::command('backup:database')->dailyAt('02:00')->withoutOverlapping();
Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/queue.log'));

// HQ System Schedules
Schedule::command('hq:telemetry')
    ->cron('*/' . config('hq.scheduler.telemetry_interval', 60) . ' * * * *')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hq_scheduler.log'));

Schedule::command('hq:heartbeat')
    ->cron('*/' . config('hq.scheduler.heartbeat_interval', 30) . ' * * * *')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hq_scheduler.log'));

Schedule::command('hq:sync')
    ->cron('*/' . config('hq.scheduler.sync_interval', 15) . ' * * * *')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hq_scheduler.log'));

Schedule::command('storage:clean-temp')->dailyAt('03:00')->withoutOverlapping();
Schedule::command('system:collect-metrics')->dailyAt('23:59')->withoutOverlapping();

Schedule::command('hq:license-check')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hq_scheduler.log'));

Schedule::call(fn () => app(\App\Domain\HQ\Services\HQSchedulerService::class)->runHourlyChecks())
    ->hourly()
    ->name('hq:hourly-checks')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hq_scheduler.log'));

Schedule::call(fn () => app(\App\Domain\HQ\Services\HQSchedulerService::class)->runDailyBillingChecks())
    ->dailyAt('00:05')
    ->name('hq:daily-billing-checks')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/hq_billing.log'));
