<?php

namespace App\Domain\Identity\Services;

use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQUserSession;
use App\Events\SessionRevoked;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SessionService
{
    protected $auditService;

    public function __construct(IdentityAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function createSession(User $user, ?HQTenant $tenant, string $token, string $ip, ?string $device): HQUserSession
    {
        $session = HQUserSession::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant ? $tenant->id : null,
            'token_hash' => Hash::make($token),
            'device' => $device,
            'ip' => $ip,
            'last_activity_at' => now(),
            'expires_at' => now()->addHours(config('sanctum.expiration', 24)),
        ]);

        $this->auditService->logSecurityEvent($tenant, $user, 'session.created', ['session_id' => $session->id, 'device' => $device]);

        return $session;
    }

    public function getActiveSessions(User $user, ?HQTenant $tenant)
    {
        return HQUserSession::where('user_id', $user->id)
            ->where('tenant_id', $tenant ? $tenant->id : null)
            ->where('expires_at', '>', now())
            ->get();
    }

    public function revokeSession(HQUserSession $session)
    {
        $session->delete();

        event(new SessionRevoked($session->user, $session->tenant, $session->id));
        $this->auditService->logSecurityEvent($session->tenant, $session->user, 'session.revoked', ['session_id' => $session->id]);
    }
}
