<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupFailedDetected
{
    use Dispatchable, SerializesModels;

    public $systemInstance;
    public $backupDetails;

    public function __construct($systemInstance, array $backupDetails)
    {
        $this->systemInstance = $systemInstance;
        $this->backupDetails = $backupDetails;
    }
}
