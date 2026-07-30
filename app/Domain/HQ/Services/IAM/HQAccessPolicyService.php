<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\User;
use App\Models\HQAccessPolicy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class HQAccessPolicyService
{
    /**
     * Evaluate ABAC policies for a user within a tenant context
     */
    public function evaluatePolicy(User $user, ?int $tenantId, string $resourceModule): bool
    {
        $policies = HQAccessPolicy::where('is_active', true)
            ->where(function ($query) use ($tenantId) {
                $query->whereNull('tenant_id');
                if ($tenantId) {
                    $query->orWhere('tenant_id', $tenantId);
                }
            })
            ->get();

        if ($policies->isEmpty()) {
            return true; // Default allow if no specific policies
        }

        $ip = Request::ip();
        $now = Carbon::now();

        foreach ($policies as $policy) {
            // Check IP Restrictions
            if (!empty($policy->ip_restrictions) && !$this->checkIpRestriction($ip, $policy->ip_restrictions)) {
                return false;
            }

            // Check Time Restrictions
            if (!empty($policy->time_restrictions) && !$this->checkTimeRestriction($now, $policy->time_restrictions)) {
                return false;
            }

            // Check Resource Restrictions
            if (!empty($policy->resource_restrictions) && in_array($resourceModule, $policy->resource_restrictions)) {
                // If it matches resource restrictions explicitly defined to block, we might block.
                // Assuming existence in this array means they are ALLOWED only to these modules.
                // Or DENIED. Let's assume ALLOW list. If not in allow list, false.
                // For simplicity, if resource_restrictions is populated, it MUST contain the requested module.
                if (!in_array($resourceModule, $policy->resource_restrictions)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function checkIpRestriction(string $ip, array $allowedIps): bool
    {
        foreach ($allowedIps as $allowedIp) {
            if ($ip === $allowedIp) { // Can be expanded for CIDR
                return true;
            }
        }
        return false;
    }

    private function checkTimeRestriction(Carbon $now, array $allowedTimes): bool
    {
        // Simple logic: e.g., ['start' => '09:00', 'end' => '18:00']
        if (isset($allowedTimes['start']) && isset($allowedTimes['end'])) {
            $start = Carbon::parse($allowedTimes['start']);
            $end = Carbon::parse($allowedTimes['end']);
            
            $currentTime = $now->format('H:i');
            return $currentTime >= $allowedTimes['start'] && $currentTime <= $allowedTimes['end'];
        }
        
        return true;
    }
}
