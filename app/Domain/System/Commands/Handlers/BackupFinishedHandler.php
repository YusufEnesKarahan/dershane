<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;
use App\Models\BackupCache;

class BackupFinishedHandler implements RemoteCommandHandlerInterface
{
    public function handle(HQCentralCommand $command): array
    {
        // HQ informs ERP that backup is recorded and finished.
        BackupCache::updateOrCreate(
            ['system_uuid' => $command->system_instance_id],
            [
                'status' => 'completed',
                'last_backup_at' => now(),
            ]
        );

        return [
            'success' => true,
            'message' => 'Backup marked as finished locally.',
        ];
    }
}
