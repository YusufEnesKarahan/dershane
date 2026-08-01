<?php

namespace App\Domain\Portal\Services;

use App\Models\Institution;
use App\Models\InstitutionPlan;
use App\Models\HQUsageRecord;
use App\Models\PortalActivityLog;
use App\Core\Services\Billing\EntitlementService;

class TenantDashboardService
{
    protected $entitlementService;

    public function __construct(EntitlementService $entitlementService)
    {
        $this->entitlementService = $entitlementService;
    }

    public function getDashboardData(Institution $tenant): array
    {
        $activeSubscription = $tenant->subscriptions()->where('status', 'active')->with('plan')->first();
        
        $usage = HQUsageRecord::where('tenant_id', $tenant->id)
            ->where('period', now()->format('Y-m'))
            ->get();

        $recentActivity = PortalActivityLog::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'tenant' => [
                'name' => $tenant->name,
                'slug' => $tenant->slug,
            ],
            'subscription' => $activeSubscription ? [
                'plan_name' => $activeSubscription->plan->name,
                'status' => $activeSubscription->status,
                'starts_at' => $activeSubscription->starts_at,
                'expires_at' => $activeSubscription->expires_at,
            ] : null,
            'usage' => $usage,
            'entitlements_summary' => $this->getEntitlementsSummary($tenant),
            'recent_activity' => $recentActivity,
        ];
    }

    protected function getEntitlementsSummary(Institution $tenant): array
    {
        // Sample of checking key entitlements
        $features = [
            'advanced_analytics' => $this->entitlementService->hasAccess($tenant, 'advanced_analytics'),
            'priority_support' => $this->entitlementService->hasAccess($tenant, 'priority_support'),
        ];
        
        return $features;
    }
}
