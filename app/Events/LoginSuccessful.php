<?php

namespace App\Events;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoginSuccessful
{
    use Dispatchable, SerializesModels;

    public $user;
    public $tenant;
    public $ip;
    public $device;

    public function __construct(User $user, ?Institution $tenant, string $ip, ?string $device)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->ip = $ip;
        $this->device = $device;
    }
}
