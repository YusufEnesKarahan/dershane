<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\SchedulerService;
use App\Domain\License\Services\LicenseVerificationService;

class HQLicenseCheckCommand extends Command
{
    protected $signature = 'hq:license-check';
    protected $description = 'Validate license with HQ and refresh local cache';

    public function handle(SchedulerService $schedulerService, LicenseVerificationService $licenseService)
    {
        if (!config('hq.scheduler.enabled')) {
            $this->info('HQ Scheduler is currently disabled. Skipping hq:license-check.');
            return 0;
        }

        $this->info('Running HQ License Check...');

        $success = $schedulerService->executeTask('hq:license-check', function () use ($licenseService) {
            $refreshed = $licenseService->refresh();
            return [
                'refreshed' => $refreshed,
                'status' => $refreshed ? 'License cache updated' : 'License refresh failed (HQ unreachable or no license)',
            ];
        });

        if ($success) {
            $this->info('HQ License Check completed successfully.');
            return 0;
        }

        $this->error('HQ License Check failed.');
        return 1;
    }
}
