<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;
use App\Domain\System\Services\ConfigurationSynchronizationService;

class ClearConfigurationCacheHandler implements RemoteCommandHandlerInterface
{
    public function __construct(
        protected ConfigurationSynchronizationService $syncService
    ) {}

    public function handle(HQCentralCommand $command): array
    {
        $this->syncService->clearCache();

        // Optionally, fetch immediately after clearing.
        $this->syncService->syncFromHQ();

        return [
            'success' => true,
            'message' => 'Configuration cache cleared and re-synced successfully.',
        ];
    }
}
