<?php

namespace App\Events;

use App\Models\HQConfigurationRollback;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConfigurationRollbackCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rollback;

    public function __construct(HQConfigurationRollback $rollback)
    {
        $this->rollback = $rollback;
    }
}
