<?php

namespace App\Events;

use App\Models\HQSlaViolation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SLAViolationDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $violation;

    public function __construct(HQSlaViolation $violation)
    {
        $this->violation = $violation;
    }
}
