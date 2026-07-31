<?php

namespace App\Events;

use App\Models\User;
use App\Models\HQTenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionRevoked
{
    use Dispatchable, SerializesModels;

    public $user;
    public $tenant;
    public $sessionId;

    public function __construct(User $user, ?HQTenant $tenant, $sessionId)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->sessionId = $sessionId;
    }
}
