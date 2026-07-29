<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\Contracts\RemoteCommandHandlerInterface;
use App\Models\HQCentralCommand;
use App\Models\BackupCache;

class BackupCheckHandler implements RemoteCommandHandlerInterface
{
    public function handle(HQCentralCommand $command): array
    {
        // Get the latest cache
        $cache = BackupCache::where('system_uuid', $command->system_instance_id)->first();
        
        return [
            'success' => true,
            'message' => 'Backup check completed.',
            'status' => $cache ? $cache->status : 'unknown',
            'last_backup_at' => $cache ? $cache->last_backup_at : null,
        ];
    }
}
