<?php

namespace App\Events;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionRevoked
{
    use Dispatchable, SerializesModels;

    public $user;
    public $tenant;
    public $sessionId;

    public function __construct(User $user, ?Institution $tenant, $sessionId)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->sessionId = $sessionId;
    }
}
