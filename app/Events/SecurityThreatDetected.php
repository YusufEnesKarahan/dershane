<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SecurityThreatDetected
{
    use Dispatchable, SerializesModels;

    public $systemInstance;
    public $threatDetails;

    public function __construct($systemInstance, array $threatDetails)
    {
        $this->systemInstance = $systemInstance;
        $this->threatDetails = $threatDetails;
    }
}
