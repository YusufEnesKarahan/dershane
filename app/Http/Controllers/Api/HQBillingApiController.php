<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\HQEntitlementService;
use App\Models\HQSystemInstance;

class HQBillingApiController extends Controller
{
    /**
     * Get the subscription status and features/limits for an instance.
     */
    public function subscriptionStatus(Request $request, HQEntitlementService $entitlementService)
    {
        $instance = HQSystemInstance::where('system_uuid', $request->system_id)->first();
        
        if (!$instance) {
            return response()->json(['status' => 'error', 'message' => 'System not found'], 404);
        }

        $tenant = $instance->tenant;
        if (!$tenant) {
            return response()->json(['status' => 'error', 'message' => 'Tenant not found'], 404);
        }

        $subscription = app(\App\Domain\HQ\Services\HQSubscriptionService::class)->getActiveSubscription($tenant);

        if (!$subscription || !$subscription->plan) {
            return response()->json([
                'status' => 'inactive',
                'message' => 'No active subscription found.',
                'features' => [],
                'limits' => [],
            ]);
        }

        return response()->json([
            'plan' => $subscription->plan->slug,
            'status' => $subscription->status,
            'features' => $subscription->plan->features ?? [],
            'limits' => $subscription->plan->limits ?? [],
        ]);
    }
}
