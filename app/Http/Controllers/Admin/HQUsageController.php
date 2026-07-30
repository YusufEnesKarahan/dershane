<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQTenant;
use App\Models\HQUsageSnapshot;
use App\Models\HQQuotaRule;
use App\Models\HQQuotaViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HQUsageController extends Controller
{
    /**
     * View tenant specific usage details.
     */
    public function show(HQTenant $tenant)
    {
        Gate::authorize('hq.viewBilling'); // Or create a new hq.viewUsage permission if required, but Billing fits this scope usually

        $dailySnapshots = HQUsageSnapshot::where('tenant_id', $tenant->id)
            ->where('period', 'daily')
            ->orderByDesc('period_start')
            ->take(30)
            ->get();

        $activeViolations = HQQuotaViolation::where('tenant_id', $tenant->id)
            ->whereNull('resolved_at')
            ->orderByDesc('created_at')
            ->get();
            
        $historicalViolations = HQQuotaViolation::where('tenant_id', $tenant->id)
            ->whereNotNull('resolved_at')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        $customRules = HQQuotaRule::where('tenant_id', $tenant->id)->get();

        $planLimits = app(\App\Domain\HQ\Services\HQEntitlementService::class)->getLimits($tenant);
        $subscription = app(\App\Domain\HQ\Services\HQSubscriptionService::class)->getActiveSubscription($tenant);

        return view('admin.hq.tenants.usage', compact(
            'tenant', 
            'dailySnapshots', 
            'activeViolations', 
            'historicalViolations',
            'customRules',
            'planLimits',
            'subscription'
        ));
    }
}
