<?php

namespace App\Domain\Platform\Services;

use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\PlatformAuditLog;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionManagementService
{
    public function createPlan(array $data): Plan
    {
        $plan = Plan::create($this->normalizePlanData($data));

        PlatformAuditLog::record(auth()->user(), 'subscription.plan.created', $plan, [
            'description' => 'Plan oluşturuldu.',
            'plan_name' => $plan->name,
        ]);

        return $plan;
    }

    public function updatePlan(Plan $plan, array $data): Plan
    {
        $plan->update($this->normalizePlanData($data, $plan));

        PlatformAuditLog::record(auth()->user(), 'subscription.plan.updated', $plan, [
            'description' => 'Plan güncellendi.',
            'plan_name' => $plan->name,
        ]);

        return $plan;
    }

    public function activatePlan(Plan $plan): Plan
    {
        $plan->update(['is_active' => true]);

        PlatformAuditLog::record(auth()->user(), 'subscription.plan.activated', $plan, [
            'description' => 'Plan aktif edildi.',
            'plan_name' => $plan->name,
        ]);

        return $plan;
    }

    public function deactivatePlan(Plan $plan): Plan
    {
        $plan->update(['is_active' => false]);

        PlatformAuditLog::record(auth()->user(), 'subscription.plan.deactivated', $plan, [
            'description' => 'Plan pasif edildi.',
            'plan_name' => $plan->name,
        ]);

        return $plan;
    }

    public function assignPlanToTenant(Branch $branch, Plan $plan, array $metadata = []): Subscription
    {
        $subscription = $branch->subscriptions()->latest('id')->first();

        if ($subscription) {
            return $this->changeSubscriptionPlan($branch, $plan, $metadata);
        }

        return DB::transaction(function () use ($branch, $plan, $metadata) {
            $startsAt = now();
            $trialDays = (int) ($plan->trial_days ?: 0);
            $isTrial = $trialDays > 0;
            $expiresAt = $this->resolveExpiry($plan, $startsAt, $isTrial ? $trialDays : null);
            $license = $this->resolveSystemLicense();

            $subscription = Subscription::create([
                'license_id' => $license->id,
                'branch_id' => $branch->id,
                'plan_id' => $plan->id,
                'status' => $isTrial ? 'trial' : 'active',
                'started_at' => $startsAt,
                'starts_at' => $startsAt,
                'trial_ends_at' => $isTrial ? $startsAt->copy()->addDays($trialDays) : null,
                'expires_at' => $expiresAt,
                'ends_at' => $expiresAt,
                'price' => $plan->price,
            ]);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'action' => 'created',
                'new_plan_id' => $plan->id,
                'metadata' => array_merge([
                    'branch_id' => $branch->id,
                    'plan_name' => $plan->name,
                ], $metadata),
            ]);

            PlatformAuditLog::record(auth()->user(), 'subscription.assigned', $branch, [
                'description' => 'Tenant plana geçirildi.',
                'old_plan' => null,
                'new_plan' => $plan->name,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription;
        });
    }

    public function upgradeSubscription(Branch $branch, Plan $newPlan, array $metadata = []): Subscription
    {
        return $this->changeSubscriptionPlan($branch, $newPlan, array_merge($metadata, ['action' => 'upgraded']));
    }

    public function downgradeSubscription(Branch $branch, Plan $newPlan, array $metadata = []): Subscription
    {
        return $this->changeSubscriptionPlan($branch, $newPlan, array_merge($metadata, ['action' => 'downgraded']));
    }

    public function changeSubscriptionPlan(Branch $branch, Plan $newPlan, array $metadata = []): Subscription
    {
        $subscription = $branch->subscriptions()->latest('id')->first();

        if (!$subscription) {
            return $this->assignPlanToTenant($branch, $newPlan, $metadata);
        }

        $oldPlan = $subscription->plan;
        $action = $metadata['action'] ?? ($oldPlan && $newPlan->price > $oldPlan->price ? 'upgraded' : 'downgraded');

        return DB::transaction(function () use ($subscription, $branch, $oldPlan, $newPlan, $action, $metadata) {
            $now = now();
            $expiresAt = $this->resolveExpiry($newPlan, $now);

            $subscription->update([
                'plan_id' => $newPlan->id,
                'status' => 'active',
                'started_at' => $subscription->started_at ?? $now,
                'starts_at' => $subscription->starts_at ?? $now,
                'trial_ends_at' => null,
                'expires_at' => $expiresAt,
                'ends_at' => $expiresAt,
                'price' => $newPlan->price,
            ]);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'old_plan_id' => $oldPlan?->id,
                'new_plan_id' => $newPlan->id,
                'action' => $action,
                'metadata' => array_merge([
                    'branch_id' => $branch->id,
                    'old_plan' => $oldPlan?->name,
                    'new_plan' => $newPlan->name,
                ], $metadata),
            ]);

            PlatformAuditLog::record(auth()->user(), 'subscription.' . $action, $branch, [
                'description' => $action === 'upgraded' ? 'Plan yükseltildi.' : 'Plan düşürüldü.',
                'old_plan' => $oldPlan?->name,
                'new_plan' => $newPlan->name,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription->fresh(['plan']);
        });
    }

    public function cancelSubscription(Branch $branch, string $reason = null): ?Subscription
    {
        $subscription = $branch->subscriptions()->latest('id')->first();

        if (!$subscription) {
            return null;
        }

        $subscription->update([
            'status' => 'cancelled',
            'canceled_at' => now(),
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'expires_at' => now(),
            'ends_at' => now(),
        ]);

        SubscriptionHistory::create([
            'subscription_id' => $subscription->id,
            'old_plan_id' => $subscription->plan_id,
            'new_plan_id' => null,
            'action' => 'cancelled',
            'metadata' => [
                'branch_id' => $branch->id,
                'reason' => $reason,
            ],
        ]);

        PlatformAuditLog::record(auth()->user(), 'subscription.cancelled', $branch, [
            'description' => 'Subscription iptal edildi.',
            'reason' => $reason,
            'subscription_id' => $subscription->id,
        ]);

        return $subscription->fresh();
    }

    public function renewSubscription(Branch $branch, array $metadata = []): ?Subscription
    {
        $subscription = $branch->subscriptions()->latest('id')->first();

        if (!$subscription || !$subscription->plan) {
            return null;
        }

        return DB::transaction(function () use ($subscription, $branch, $metadata) {
            $now = now();
            $expiresAt = $this->resolveExpiry($subscription->plan, $subscription->expires_at ?? $now);

            $subscription->update([
                'status' => 'active',
                'started_at' => $subscription->started_at ?? $now,
                'starts_at' => $subscription->starts_at ?? $now,
                'expires_at' => $expiresAt,
                'ends_at' => $expiresAt,
                'canceled_at' => null,
                'cancelled_at' => null,
                'cancellation_reason' => null,
            ]);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'old_plan_id' => $subscription->plan_id,
                'new_plan_id' => $subscription->plan_id,
                'action' => 'renewed',
                'metadata' => array_merge([
                    'branch_id' => $branch->id,
                    'plan_name' => $subscription->plan->name,
                ], $metadata),
            ]);

            PlatformAuditLog::record(auth()->user(), 'subscription.renewed', $branch, [
                'description' => 'Subscription yenilendi.',
                'plan' => $subscription->plan->name,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription->fresh(['plan']);
        });
    }

    public function createTrialSubscription(Branch $branch, Plan $plan, ?int $trialDays = null): Subscription
    {
        $trialDays = $trialDays ?? (int) ($plan->trial_days ?: 0);

        return DB::transaction(function () use ($branch, $plan, $trialDays) {
            $startsAt = now();
            $expiresAt = $startsAt->copy()->addDays(max(1, $trialDays));
            $license = $this->resolveSystemLicense();

            $subscription = Subscription::create([
                'license_id' => $license->id,
                'branch_id' => $branch->id,
                'plan_id' => $plan->id,
                'status' => 'trial',
                'started_at' => $startsAt,
                'starts_at' => $startsAt,
                'trial_ends_at' => $expiresAt,
                'expires_at' => $expiresAt,
                'ends_at' => $expiresAt,
                'price' => $plan->price,
            ]);

            SubscriptionHistory::create([
                'subscription_id' => $subscription->id,
                'action' => 'created',
                'new_plan_id' => $plan->id,
                'metadata' => [
                    'branch_id' => $branch->id,
                    'trial_days' => $trialDays,
                    'plan_name' => $plan->name,
                ],
            ]);

            PlatformAuditLog::record(auth()->user(), 'subscription.trial.created', $branch, [
                'description' => 'Trial aboneliği oluşturuldu.',
                'trial_days' => $trialDays,
                'new_plan' => $plan->name,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription;
        });
    }

    public function checkExpiredSubscriptions(): int
    {
        $expiredCount = 0;

        Subscription::query()
            ->tenant()
            ->whereIn('status', ['trial', 'trialing', 'active'])
            ->where(function ($query) {
                $query->whereNotNull('expires_at')->where('expires_at', '<', now())
                    ->orWhere(function ($nestedQuery) {
                        $nestedQuery->whereNull('expires_at')
                            ->whereNotNull('trial_ends_at')
                            ->where('trial_ends_at', '<', now());
                    });
            })
            ->chunkById(50, function ($subscriptions) use (&$expiredCount) {
                foreach ($subscriptions as $subscription) {
                    $subscription->update(['status' => 'expired']);
                    SubscriptionHistory::create([
                        'subscription_id' => $subscription->id,
                        'old_plan_id' => $subscription->plan_id,
                        'new_plan_id' => null,
                        'action' => 'expired',
                        'metadata' => [
                            'branch_id' => $subscription->branch_id,
                        ],
                    ]);
                    $expiredCount++;
                }
            });

        return $expiredCount;
    }

    protected function normalizePlanData(array $data, ?Plan $plan = null): array
    {
        $name = $data['name'] ?? $plan?->name;
        $slug = $data['slug'] ?? $plan?->slug ?? Str::slug((string) $name);
        $billingCycle = $data['billing_cycle'] ?? $data['billing_period'] ?? $plan?->billing_cycle ?? $plan?->billing_period ?? 'monthly';
        $features = $this->normalizeFeatures($data['features'] ?? $plan?->features ?? []);

        return [
            'uuid' => $plan?->uuid ?? (string) Str::uuid(),
            'name' => $name,
            'slug' => $slug,
            'description' => $data['description'] ?? $plan?->description,
            'price' => $data['price'] ?? $plan?->price ?? 0,
            'billing_period' => $billingCycle,
            'billing_cycle' => $billingCycle,
            'trial_days' => (int) ($data['trial_days'] ?? $plan?->trial_days ?? 0),
            'max_students' => $this->nullableInteger($data['max_students'] ?? $plan?->max_students ?? null),
            'max_users' => $this->nullableInteger($data['max_users'] ?? $plan?->max_users ?? null),
            'max_teachers' => $this->nullableInteger($data['max_teachers'] ?? $plan?->max_teachers ?? null),
            'is_active' => filter_var($data['is_active'] ?? $plan?->is_active ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'features' => $features,
            'limits' => [
                'students' => $this->nullableInteger($data['max_students'] ?? $plan?->max_students ?? data_get($plan?->limits, 'students')),
                'users' => $this->nullableInteger($data['max_users'] ?? $plan?->max_users ?? data_get($plan?->limits, 'users')),
                'teachers' => $this->nullableInteger($data['max_teachers'] ?? $plan?->max_teachers ?? data_get($plan?->limits, 'teachers')),
                'grace_days' => $this->nullableInteger($data['grace_days'] ?? data_get($plan?->limits, 'grace_days', 0)),
            ],
        ];
    }

    protected function normalizeFeatures(mixed $features): array
    {
        if (is_array($features)) {
            return array_values(array_filter($features, fn ($feature) => $feature !== null && $feature !== ''));
        }

        if (is_string($features)) {
            $parts = preg_split('/[\r\n,]+/', $features) ?: [];
            return array_values(array_filter(array_map('trim', $parts)));
        }

        return [];
    }

    protected function nullableInteger(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }

    protected function resolveExpiry(Plan $plan, Carbon $from, ?int $trialDays = null): Carbon
    {
        if ($trialDays !== null) {
            return $from->copy()->addDays(max(1, $trialDays));
        }

        $billingCycle = $plan->billing_cycle ?: $plan->billing_period ?: 'monthly';

        return match ($billingCycle) {
            'yearly' => $from->copy()->addYear(),
            default => $from->copy()->addMonth(),
        };
    }

    protected function resolveSystemLicense(): License
    {
        return License::query()->firstOrFail();
    }
}