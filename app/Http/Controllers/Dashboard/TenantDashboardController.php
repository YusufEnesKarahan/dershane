<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Tenant\Services\TenantDashboardService;
use App\Core\Context\TenantContext;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{
    protected TenantDashboardService $dashboardService;

    public function __construct(TenantDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $branchId = TenantContext::getActiveBranchId();
        
        if (!$branchId) {
            abort(403, 'Tenant context missing.');
        }

        $data = $this->dashboardService->getDashboardData($branchId);

        // Calculate subscription limits (e.g., fetch from current tenant's subscription/plan)
        // Here we just fetch current branch info and mock or compute from limits
        // If we had a SubscriptionService, we'd use that.
        // For now, we will add 'limits' directly to $data if needed or fetch branch->subscription
        $branch = \App\Models\Branch::with('subscription.plan')->find($branchId);
        $subscription = $branch?->subscription;
        $plan = $subscription?->plan;
        
        $limits = [
            'students' => [
                'current' => $data['statistics']['students'] ?? 0,
                'max' => $plan?->max_students ?? 100, // fallback 100
            ],
            'teachers' => [
                'current' => $data['statistics']['teachers'] ?? 0,
                'max' => $plan?->max_teachers ?? 20, // fallback 20
            ],
            'days_left' => $subscription?->ends_at ? now()->diffInDays($subscription->ends_at, false) : 30,
            'plan_name' => $plan?->name ?? 'Standart Plan',
        ];

        return view('dashboard.index', array_merge($data, ['limits' => $limits]));
    }
}
