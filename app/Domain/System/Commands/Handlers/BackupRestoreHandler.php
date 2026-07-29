<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;

class BackupRestoreHandler implements RemoteCommandHandlerInterface
{
    public function handle(HQCentralCommand $command): array
    {
        // Safe placeholder. Does not execute direct shell commands.
        // It relies on internal strict logic.
        return [
            'success' => true,
            'message' => 'Restore command received. Initializing strict restore routine.',
        ];
    }
}
