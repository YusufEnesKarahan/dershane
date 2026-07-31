<?php

namespace App\Events;

use App\Models\HQTenantInvitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationSent
{
    use Dispatchable, SerializesModels;

    public $invitation;

    public function __construct(HQTenantInvitation $invitation)
    {
        $this->invitation = $invitation;
    }
}
