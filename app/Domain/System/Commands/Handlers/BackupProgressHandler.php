<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;

class BackupProgressHandler implements RemoteCommandHandlerInterface
{
    public function handle(HQCentralCommand $command): array
    {
        // Handled directly via the API endpoints usually, but can be triggered if HQ requests progress
        return [
            'success' => true,
            'message' => 'Progress query handled.',
        ];
    }
}
