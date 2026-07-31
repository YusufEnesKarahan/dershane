<?php

namespace App\Domain\Identity\Services;

use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQUserSecurity;
use App\Models\HQLoginAttempt;
use App\Events\LoginSuccessful;
use App\Events\LoginFailed;
use App\Events\AccountLocked;
use Illuminate\Support\Facades\Hash;

class LoginSecurityService
{
    protected $auditService;
    const MAX_FAILED_ATTEMPTS = 5;
    const LOCKOUT_MINUTES = 15;

    public function __construct(IdentityAuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function checkLogin(User $user, ?HQTenant $tenant, string $password, string $ip, ?string $device): bool
    {
        $security = HQUserSecurity::firstOrCreate([
            'user_id' => $user->id,
            'tenant_id' => $tenant ? $tenant->id : null,
        ]);

        if ($security->locked_until && $security->locked_until > now()) {
            $this->logAttempt($user, $tenant, $ip, false, ['reason' => 'account_locked']);
            return false;
        }

        if (Hash::check($password, $user->password)) {
            $this->handleSuccessfulLogin($user, $tenant, $security, $ip, $device);
            return true;
        }

        $this->handleFailedLogin($user, $tenant, $security, $ip, $device);
        return false;
    }

    protected function handleSuccessfulLogin(User $user, ?HQTenant $tenant, HQUserSecurity $security, string $ip, ?string $device)
    {
        $security->update([
            'failed_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_ip' => $ip,
        ]);

        $this->logAttempt($user, $tenant, $ip, true);

        event(new LoginSuccessful($user, $tenant, $ip, $device));
        $this->auditService->logSecurityEvent($tenant, $user, 'login.success', ['ip' => $ip, 'device' => $device]);
        
        \App\Jobs\AnalyzeLoginRiskJob::dispatch($user, $tenant, $ip);
    }

    protected function handleFailedLogin(User $user, ?HQTenant $tenant, HQUserSecurity $security, string $ip, ?string $device)
    {
        $security->increment('failed_attempts');

        if ($security->failed_attempts >= self::MAX_FAILED_ATTEMPTS) {
            $lockedUntil = now()->addMinutes(self::LOCKOUT_MINUTES);
            $security->update(['locked_until' => $lockedUntil]);
            event(new AccountLocked($user, $tenant, $lockedUntil));
            $this->auditService->logSecurityEvent($tenant, $user, 'account.locked', ['ip' => $ip, 'locked_until' => $lockedUntil]);
        }

        $this->logAttempt($user, $tenant, $ip, false, ['reason' => 'invalid_credentials']);

        event(new LoginFailed($user, $tenant, $ip, $device));
        $this->auditService->logSecurityEvent($tenant, $user, 'login.failed', ['ip' => $ip, 'device' => $device, 'attempts' => $security->failed_attempts]);
    }

    protected function logAttempt(?User $user, ?HQTenant $tenant, string $ip, bool $success, array $metadata = [])
    {
        HQLoginAttempt::create([
            'user_id' => $user ? $user->id : null,
            'tenant_id' => $tenant ? $tenant->id : null,
            'ip' => $ip,
            'success' => $success,
            'metadata' => $metadata,
        ]);
    }
}
