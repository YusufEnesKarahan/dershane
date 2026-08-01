<?php

namespace App\Events;

use App\Models\User;
use App\Models\Institution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountLocked
{
    use Dispatchable, SerializesModels;

    public $user;
    public $tenant;
    public $lockedUntil;

    public function __construct(User $user, ?Institution $tenant, \DateTimeInterface $lockedUntil)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->lockedUntil = $lockedUntil;
    }
}
