<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\User;
use App\Models\HQLoginAttempt;
use Illuminate\Support\Facades\Request;
use App\Domain\HQ\Services\HQAlertService;
use App\Domain\HQ\Services\HQAuditService;

class LoginSecurityService
{
    public function recordLoginAttempt(?User $user, bool $isSuccessful, array $context = []): void
    {
        $ip = Request::ip();
        
        HQLoginAttempt::create([
            'user_id' => $user?->id,
            'ip_address' => $ip,
            'user_agent' => Request::userAgent(),
            'is_successful' => $isSuccessful,
            'context' => $context
        ]);

        if (!$isSuccessful && $user) {
            $this->checkBruteForce($user, $ip);
            
            event(new \App\Events\SuspiciousLoginDetected($user, $ip));
        }

        if ($isSuccessful && $user) {
            app(HQAuditService::class)->logSystemAction(
                action: 'user_login',
                category: 'iam',
                severity: 'info',
                description: "User {$user->id} logged in successfully.",
                tenantId: $user->tenant_id ?? null
            );
        }
    }

    private function checkBruteForce(User $user, string $ip): void
    {
        $recentFailures = HQLoginAttempt::where('user_id', $user->id)
            ->where('is_successful', false)
            ->where('attempted_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentFailures >= 5) {
            app(HQAlertService::class)->createAlert(
                severity: 'critical',
                title: 'login.bruteforce',
                message: "Multiple failed login attempts for user {$user->id} from IP {$ip}",
                tenantId: $user->tenant_id ?? null
            );
        }
    }
}
