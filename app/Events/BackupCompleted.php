<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\HQBackupJob;

class BackupCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action, // e.g. 'backup.started', 'backup.completed', 'backup.failed', 'backup.restored'
        public HQBackupJob $job,
        public ?string $description = null
    ) {}
}
