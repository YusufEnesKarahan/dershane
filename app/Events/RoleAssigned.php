<?php

namespace App\Events;

use App\Models\User;
use App\Models\HQRole;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleAssigned
{
    use Dispatchable, SerializesModels;

    public $user;
    public $role;

    public function __construct(User $user, HQRole $role)
    {
        $this->user = $user;
        $this->role = $role;
    }
}
