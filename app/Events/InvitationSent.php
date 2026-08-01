<?php

namespace App\Events;

use App\Models\InstitutionInvitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationSent
{
    use Dispatchable, SerializesModels;

    public $invitation;

    public function __construct(InstitutionInvitation $invitation)
    {
        $this->invitation = $invitation;
    }
}
