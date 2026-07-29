<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemOfflineDetected
{
    use Dispatchable, SerializesModels;

    public $systemInstance;
    public $minutesOffline;

    public function __construct($systemInstance, int $minutesOffline)
    {
        $this->systemInstance = $systemInstance;
        $this->minutesOffline = $minutesOffline;
    }
}
