<?php

namespace App\Domain\Onboarding\Services;

use App\Models\HQTenant;
use App\Models\HQTenantInvitation;
use App\Models\Role;
use Illuminate\Support\Str;
use App\Domain\HQ\Services\HQAuditService;
use Exception;

class InvitationService
{
    protected $auditService;

    public function __construct(HQAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function inviteUser(HQTenant $tenant, string $email, ?Role $role): HQTenantInvitation
    {
        $invitation = HQTenantInvitation::create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'role_id' => $role ? $role->id : null,
            'token_hash' => hash('sha256', Str::random(40)),
            'expires_at' => now()->addDays(7),
        ]);

        \App\Jobs\SendInvitationJob::dispatch($invitation->id);
        
        $this->auditService->logSystemAction('invitation_created', 'onboarding', 'info', "Invitation created for {$email} in Tenant {$tenant->id}.");

        return $invitation;
    }

    public function acceptInvitation(string $tokenHash, \App\Models\User $user)
    {
        $invitation = HQTenantInvitation::where('token_hash', $tokenHash)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            throw new Exception("Invalid or expired invitation.");
        }

        $invitation->update(['accepted_at' => now()]);

        // In a full implementation, you would attach the user to the tenant and role here.
        // E.g. $user->roles()->attach($invitation->role_id, ['tenant_id' => $invitation->tenant_id]);

        event(new \App\Events\InvitationAccepted($invitation, $user));
        $this->auditService->logSystemAction('invitation_accepted', 'onboarding', 'info', "User {$user->id} accepted invitation for Tenant {$invitation->tenant_id}.");

        return $invitation;
    }
}
