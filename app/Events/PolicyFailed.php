<?php

namespace App\Events;

use App\Models\HQPolicy;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PolicyFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $policy;
    public $context;

    public function __construct(HQPolicy $policy, array $context)
    {
        $this->policy = $policy;
        $this->context = $context;
    }
}
