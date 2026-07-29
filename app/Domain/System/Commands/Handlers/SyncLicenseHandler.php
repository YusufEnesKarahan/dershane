<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;
use App\Domain\License\LicenseManager;

class SyncLicenseHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        $refreshed = LicenseManager::refresh();
        
        return [
            'success' => $refreshed,
            'message' => $refreshed ? 'License successfully synced.' : 'Failed to sync license.',
        ];
    }
}
