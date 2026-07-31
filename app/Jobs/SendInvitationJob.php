<?php

namespace App\Jobs;

use App\Models\HQTenantInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invitationId;

    public function __construct($invitationId)
    {
        $this->invitationId = $invitationId;
    }

    public function handle(): void
    {
        $invitation = HQTenantInvitation::find($this->invitationId);
        if (!$invitation) return;

        Log::info("SendInvitationJob: Sending invitation to {$invitation->email} for tenant {$invitation->tenant_id}");
        
        // In a real application, we'd send an email here using Mail::to($invitation->email)->send(...)
        
        event(new \App\Events\InvitationSent($invitation));
    }
}
