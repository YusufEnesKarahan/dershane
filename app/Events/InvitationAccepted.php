<?php

namespace App\Events;

use App\Models\HQTenantInvitation;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAccepted
{
    use Dispatchable, SerializesModels;

    public $invitation;
    public $user;

    public function __construct(HQTenantInvitation $invitation, User $user)
    {
        $this->invitation = $invitation;
        $this->user = $user;
    }
}
