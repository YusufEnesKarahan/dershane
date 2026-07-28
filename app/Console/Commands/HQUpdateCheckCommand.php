<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Platform\Services\HQUpdateService;

class HQUpdateCheckCommand extends Command
{
    protected $signature = 'hq:update-check';
    protected $description = 'Check for new HQ updates securely';

    public function handle(HQUpdateService $updateService)
    {
        if (!config('hq.updates.enabled')) {
            $this->info('HQ Updates are disabled. Skipping check.');
            return 0;
        }

        $this->info('Checking for HQ updates...');
        
        $update = $updateService->checkAvailable();

        if ($update) {
            $this->info('New update found: v' . $update['version']);
            return 0;
        }

        $this->info('System is up to date.');
        return 0;
    }
}
