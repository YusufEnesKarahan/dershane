<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;
use App\Domain\System\Services\ConfigurationSynchronizationService;

class SyncConfigurationHandler implements RemoteCommandHandlerInterface
{
    public function __construct(
        protected ConfigurationSynchronizationService $syncService
    ) {}

    public function handle(HQCentralCommand $command): array
    {
        $success = $this->syncService->syncFromHQ();

        if ($success) {
            return [
                'success' => true,
                'message' => 'Configuration synchronized successfully.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to synchronize configuration from HQ.',
        ];
    }
}
