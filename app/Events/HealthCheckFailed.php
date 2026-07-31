<?php

namespace App\Events;

use App\Models\HQHealthCheck;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HealthCheckFailed
{
    use Dispatchable, SerializesModels;

    public $healthCheck;

    public function __construct(HQHealthCheck $healthCheck)
    {
        $this->healthCheck = $healthCheck;
    }
}
