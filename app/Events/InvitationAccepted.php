<?php

namespace App\Events;

use App\Models\InstitutionInvitation;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAccepted
{
    use Dispatchable, SerializesModels;

    public $invitation;
    public $user;

    public function __construct(InstitutionInvitation $invitation, User $user)
    {
        $this->invitation = $invitation;
        $this->user = $user;
    }
}
