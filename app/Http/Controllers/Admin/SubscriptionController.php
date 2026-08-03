<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Platform\Services\SubscriptionLimitService;
use App\Domain\Platform\Services\SubscriptionManagementService;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionManagementService $subscriptionService,
        protected SubscriptionLimitService $limitService
    ) {}

    public function index()
    {
        $metrics = [
            'total_plans' => Plan::count(),
            'active_subscriptions' => Subscription::tenant()->where('status', 'active')->count(),
            'trial_tenants' => Subscription::tenant()->where('status', 'trial')->count(),
            'monthly_revenue_estimate' => $this->estimateMonthlyRevenue(),
        ];

        $recentSubscriptions = Subscription::query()
            ->tenant()
            ->with(['branch', 'plan'])
            ->latest('id')
            ->take(10)
            ->get();

        return view('admin.platform.subscriptions.index', compact('metrics', 'recentSubscriptions'));
    }

    public function plans()
    {
        $plans = Plan::query()
            ->withCount(['subscriptions as tenant_count' => fn ($query) => $query->whereNotNull('branch_id')])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.platform.plans.index', compact('plans'));
    }

    public function createPlan()
    {
        $plan = new Plan([
            'is_active' => true,
            'billing_cycle' => 'monthly',
            'trial_days' => 14,
        ]);

        $mode = 'create';

        return view('admin.platform.plans.show', compact('plan', 'mode'));
    }

    public function storePlan(Request $request)
    {
        $data = $this->validatePlan($request);
        $plan = $this->subscriptionService->createPlan($data);

        return redirect()->route('admin.platform.subscriptions.plans')->with('success', 'Plan oluşturuldu.');
    }

    public function editPlan(Plan $plan)
    {
        $plan->loadCount(['subscriptions as tenant_count' => fn ($query) => $query->whereNotNull('branch_id')]);
        $mode = 'edit';

        return view('admin.platform.plans.show', compact('plan', 'mode'));
    }

    public function showPlan(Plan $plan)
    {
        $plan->loadCount(['subscriptions as tenant_count' => fn ($query) => $query->whereNotNull('branch_id')]);
        $plan->load(['subscriptions.branch']);
        $mode = 'show';

        return view('admin.platform.plans.show', compact('plan', 'mode'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $this->validatePlan($request);
        $this->subscriptionService->updatePlan($plan, $data);

        return redirect()->route('admin.platform.subscriptions.plans')->with('success', 'Plan güncellendi.');
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $branch = Branch::findOrFail($data['branch_id']);
        $plan = Plan::findOrFail($data['plan_id']);

        $this->subscriptionService->assignPlanToTenant($branch, $plan);

        return back()->with('success', 'Tenant plana geçirildi.');
    }

    public function changePlan(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $branch = Branch::findOrFail($data['branch_id']);
        $plan = Plan::findOrFail($data['plan_id']);
        $currentPlan = $branch->subscription()?->first()?->plan;

        if ($currentPlan && $plan->price < $currentPlan->price) {
            $this->subscriptionService->downgradeSubscription($branch, $plan);
        } else {
            $this->subscriptionService->upgradeSubscription($branch, $plan);
        }

        return back()->with('success', 'Subscription planı güncellendi.');
    }

    public function cancel(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $branch = Branch::findOrFail($data['branch_id']);
        $this->subscriptionService->cancelSubscription($branch, $data['reason'] ?? null);

        return back()->with('success', 'Subscription iptal edildi.');
    }

    protected function validatePlan(Request $request): array
    {
        $planId = $request->route('plan')?->id ?? $request->route('plan');

        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('plans', 'slug')->ignore($planId),
            ],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'trial_days' => 'nullable|integer|min:0|max:365',
            'max_students' => 'nullable|integer|min:0',
            'max_users' => 'nullable|integer|min:0',
            'max_teachers' => 'nullable|integer|min:0',
            'grace_days' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'features' => 'nullable',
        ]);
    }

    protected function estimateMonthlyRevenue(): float
    {
        return Subscription::tenant()->with('plan')->get()->sum(function (Subscription $subscription) {
            $plan = $subscription->plan;

            if (!$plan || !in_array($subscription->status, ['active', 'trial'], true)) {
                return 0;
            }

            $cycle = $plan->billing_cycle ?: $plan->billing_period;

            return $cycle === 'yearly'
                ? ((float) $subscription->price / 12)
                : (float) $subscription->price;
        });
    }
}