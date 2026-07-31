<?php

namespace App\Events;

use App\Models\HQTrace;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerformanceDegraded
{
    use Dispatchable, SerializesModels;

    public $trace;
    public $reason;

    public function __construct(HQTrace $trace, string $reason)
    {
        $this->trace = $trace;
        $this->reason = $reason;
    }
}
