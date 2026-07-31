<?php

namespace App\Events;

use App\Models\User;
use App\Models\HQTenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountLocked
{
    use Dispatchable, SerializesModels;

    public $user;
    public $tenant;
    public $lockedUntil;

    public function __construct(User $user, ?HQTenant $tenant, \DateTimeInterface $lockedUntil)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->lockedUntil = $lockedUntil;
    }
}
