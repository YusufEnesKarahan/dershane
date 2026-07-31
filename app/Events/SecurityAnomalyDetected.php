<?php

namespace App\Events;

use App\Models\HQSecurityEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SecurityAnomalyDetected
{
    use Dispatchable, SerializesModels;

    public $securityEvent;

    public function __construct(HQSecurityEvent $securityEvent)
    {
        $this->securityEvent = $securityEvent;
    }
}
