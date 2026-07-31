<?php

namespace App\Domain\HQ\Services\Observability;

use App\Models\HQSecurityEvent;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SecurityAnalyticsService
{
    /**
     * Detect and record an anomaly
     */
    public function recordAnomaly(string $type, string $severity, array $metadata = [], ?int $userId = null, ?int $tenantId = null, ?string $ip = null): HQSecurityEvent
    {
        $event = HQSecurityEvent::create([
            'event_type' => $type,
            'severity' => $severity,
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'ip' => $ip ?? request()->ip(),
            'metadata' => $metadata,
        ]);

        // Dispatch Event for Workflow engine
        event(new \App\Events\SecurityAnomalyDetected($event));

        // Alert integration will happen in listener or workflow engine
        return $event;
    }

    public function analyzeLoginFailures(User $user, string $ip): void
    {
        // Check for multiple failed logins in the last 15 minutes
        $failedCount = DB::table('hq_login_attempts')
            ->where('user_id', $user->id)
            ->where('is_successful', false)
            ->where('attempted_at', '>=', now()->subMinutes(15))
            ->count();

        if ($failedCount >= 5) {
            $this->recordAnomaly(
                type: 'multiple_failed_logins',
                severity: 'high',
                metadata: ['failed_count' => $failedCount],
                userId: $user->id,
                tenantId: $user->tenant_id ?? null,
                ip: $ip
            );
        }
    }

    public function detectApiAbuse(string $ip, string $endpoint): void
    {
        // A simple logic for API abuse could be rate limiting exceeded consistently, 
        // which can be hooked into standard RateLimiting.
        $this->recordAnomaly(
            type: 'api_abuse_detected',
            severity: 'medium',
            metadata: ['endpoint' => $endpoint],
            ip: $ip
        );
    }
}
