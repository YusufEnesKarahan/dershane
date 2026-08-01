<?php

namespace App\Domain\Identity\Services;

use App\Models\User;
use App\Models\Institution;
use App\Models\HQUserSecurity;
use App\Events\MFACreated;

class MFAService
{
    protected $auditService;

    public function __construct(IdentityAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function enableMFA(User $user, ?Institution $tenant)
    {
        $security = HQUserSecurity::firstOrCreate([
            'user_id' => $user->id,
            'tenant_id' => $tenant ? $tenant->id : null,
        ]);

        $security->update(['mfa_enabled' => true]);

        event(new MFACreated($user));
        $this->auditService->logSecurityEvent($tenant, $user, 'mfa.enabled');

        return true;
    }

    public function disableMFA(User $user, ?Institution $tenant)
    {
        $security = HQUserSecurity::where('user_id', $user->id)
            ->where('tenant_id', $tenant ? $tenant->id : null)
            ->first();

        if ($security) {
            $security->update(['mfa_enabled' => false]);
            $this->auditService->logSecurityEvent($tenant, $user, 'mfa.disabled');
        }

        return true;
    }

    public function verifyMFA(User $user, ?Institution $tenant, string $code): bool
    {
        // Placeholder for actual MFA verification (e.g., using Google Authenticator TOTP)
        // For the sake of this sprint, we'll assume a dummy verification
        // where code "123456" is always valid for testing purposes.
        $isValid = ($code === '123456');

        if ($isValid) {
            $this->auditService->logSecurityEvent($tenant, $user, 'mfa.verified');
            return true;
        }

        $this->auditService->logSecurityEvent($tenant, $user, 'mfa.failed');
        return false;
    }
}
