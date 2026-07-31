<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\Billing\BillingPlanService;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Models\HQUsageRecord;
use Exception;

class HQBillingApiController extends Controller
{
    protected $planService;
    protected $subscriptionService;

    public function __construct(BillingPlanService $planService, SubscriptionService $subscriptionService)
    {
        $this->planService = $planService;
        $this->subscriptionService = $subscriptionService;
        
        $this->middleware('hq.api.signature');
    }

    public function getPlans()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->planService->getActivePlans()
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:hq_tenants,id',
            'plan_slug' => 'required|exists:hq_plans,slug',
        ]);

        try {
            $tenant = HQTenant::findOrFail($request->tenant_id);
            $plan = HQPlan::where('slug', $request->plan_slug)->firstOrFail();

            $subscription = $this->subscriptionService->subscribe($tenant, $plan);

            return response()->json([
                'status' => 'success',
                'data' => $subscription
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:hq_tenants,id',
            'new_plan_slug' => 'required|exists:hq_plans,slug',
        ]);

        try {
            $tenant = HQTenant::findOrFail($request->tenant_id);
            $subscription = $tenant->subscriptions()->where('status', 'active')->firstOrFail();
            $newPlan = HQPlan::where('slug', $request->new_plan_slug)->firstOrFail();

            $updatedSubscription = $this->subscriptionService->upgradeOrDowngrade($subscription, $newPlan);

            return response()->json([
                'status' => 'success',
                'data' => $updatedSubscription
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:hq_tenants,id',
        ]);

        try {
            $tenant = HQTenant::findOrFail($request->tenant_id);
            $subscription = $tenant->subscriptions()->where('status', 'active')->firstOrFail();

            $cancelledSubscription = $this->subscriptionService->cancel($subscription);

            return response()->json([
                'status' => 'success',
                'data' => $cancelledSubscription
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getUsage(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:hq_tenants,id',
            'period' => 'nullable|string',
        ]);

        $period = $request->period ?? now()->format('Y-m');
        $usage = HQUsageRecord::where('tenant_id', $request->tenant_id)
                              ->where('period', $period)
                              ->get();

        return response()->json([
            'status' => 'success',
            'data' => $usage
        ]);
    }
}
