<?php

namespace App\Domain\Platform\Services;

use App\Models\Branch;
use App\Models\License;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\PlatformAuditLog;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SaaSOperationsService
{
    /**
     * Get all tenants (branches) with basic info.
     */
    public function getAllTenants($search = null)
    {
        $query = Branch::query()->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query->paginate(15);
    }

    /**
     * Get detailed statistics for a specific tenant.
     */
    public function getTenantStats(int $branchId): array
    {
        return $this->getTenantUsageStats($branchId);
    }

    /**
     * Get expanded usage details for a tenant.
     */
    public function getTenantUsageStats(int $branchId): array
    {
        $usersCount = User::withoutGlobalScopes()->where('branch_id', $branchId)->count();
        $studentsCount = Student::withoutGlobalScopes()->where('branch_id', $branchId)->count();
        $teachersCount = Teacher::withoutGlobalScopes()->where('branch_id', $branchId)->count();
        $classroomsCount = Classroom::withoutGlobalScopes()->where('branch_id', $branchId)->count();

        $lastLoginUser = User::withoutGlobalScopes()->where('branch_id', $branchId)->whereNotNull('last_login_at')
            ->orderByDesc('last_login_at')
            ->first(['id', 'name', 'last_login_at']);

        $lastActiveAt = collect([
            User::withoutGlobalScopes()->where('branch_id', $branchId)->max('last_login_at'),
            User::withoutGlobalScopes()->where('branch_id', $branchId)->max('updated_at'),
            Student::withoutGlobalScopes()->where('branch_id', $branchId)->max('updated_at'),
            Teacher::withoutGlobalScopes()->where('branch_id', $branchId)->max('updated_at'),
            Classroom::withoutGlobalScopes()->where('branch_id', $branchId)->max('updated_at'),
        ])->filter()->sortDesc()->first();

        $estimatedBytes = ($usersCount * 2800)
            + ($studentsCount * 2400)
            + ($teachersCount * 3200)
            + ($classroomsCount * 1600);

        return [
            'users_count' => $usersCount,
            'students_count' => $studentsCount,
            'teachers_count' => $teachersCount,
            'classrooms_count' => $classroomsCount,
            'last_active_at' => $lastActiveAt ? Carbon::parse($lastActiveAt) : null,
            'last_login_user' => $lastLoginUser ? [
                'id' => $lastLoginUser->id,
                'name' => $lastLoginUser->name,
                'last_login_at' => $lastLoginUser->last_login_at,
            ] : null,
            'estimated_data_size_bytes' => $estimatedBytes,
            'estimated_data_size_human' => $this->formatBytes($estimatedBytes),
        ];
    }

    /**
     * Get the system-wide license with its relationships.
     */
    public function getSystemLicense()
    {
        return License::with('planModel', 'subscription.logs', 'subscription.payments')->first();
    }

    /**
     * Suspend the system license.
     */
    public function suspendLicense(): bool
    {
        $license = License::first();
        if ($license && $license->status !== 'suspended') {
            $license->update(['status' => 'suspended']);
            
            if ($license->subscription) {
                SubscriptionLog::create([
                    'subscription_id' => $license->subscription->id,
                    'action' => 'suspended',
                    'notes' => 'System license suspended by Super Admin',
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Re-activate the system license.
     */
    public function activateLicense(): bool
    {
        $license = License::first();
        if ($license && $license->status === 'suspended') {
            $license->update(['status' => 'active']);
            
            if ($license->subscription) {
                SubscriptionLog::create([
                    'subscription_id' => $license->subscription->id,
                    'action' => 'activated',
                    'notes' => 'System license re-activated by Super Admin',
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Get recent audit activities for a tenant.
     */
    public function getTenantActivityFeed(int $branchId, int $limit = 10): Collection
    {
        $auditActivities = PlatformAuditLog::query()
            ->with('user')
            ->where('target_type', Branch::class)
            ->where('target_id', $branchId)
            ->latest('created_at')
            ->take($limit)
            ->get()
            ->map(function (PlatformAuditLog $log) {
                return [
                    'title' => $log->action,
                    'description' => $log->metadata['description'] ?? 'Operasyon kaydı oluşturuldu.',
                    'timestamp' => $log->created_at,
                    'actor' => $log->user?->name ?? 'System',
                    'type' => 'audit',
                ];
            });

        $loginActivities = User::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->whereNotNull('last_login_at')
            ->orderByDesc('last_login_at')
            ->take($limit)
            ->get(['id', 'name', 'last_login_at'])
            ->map(function (User $user) {
                return [
                    'title' => 'Kullanıcı Girişi',
                    'description' => $user->name . ' sisteme giriş yaptı.',
                    'timestamp' => $user->last_login_at,
                    'actor' => $user->name,
                    'type' => 'login',
                ];
            });

        return $auditActivities
            ->concat($loginActivities)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values();
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $value = $bytes;
        $index = 0;

        while ($value >= 1024 && $index < count($units) - 1) {
            $value /= 1024;
            $index++;
        }

        return number_format($value, $index === 0 ? 0 : 1) . ' ' . $units[$index];
    }

    public function getDashboardMetrics(): array
    {
        $totalTenants = Branch::count();
        $license = License::first();
        $tenantSubscriptions = Subscription::query()->tenant();
        $allSubscriptions = $tenantSubscriptions->get();
        
        $licenseStatus = $license ? $license->status : 'none';
        $isTrial = $license && $license->status === 'trial';
        $isSuspended = $license && $license->status === 'suspended';
        
        $expiringSoonCount = $allSubscriptions->filter(function($sub) {
            if (!$sub->expires_at) return false;
            $daysLeft = Carbon::now()->diffInDays($sub->expires_at, false);
            return $daysLeft > 0 && $daysLeft <= 7;
        })->count();

        $activeSubscriptions = $allSubscriptions->where('status', 'active');
        $graceSubscriptions = $allSubscriptions->where('status', 'grace');
        $expiredSubscriptions = $allSubscriptions->where('status', 'expired');
        $suspendedSubscriptions = $allSubscriptions->where('status', 'suspended');
        $cancelledSubscriptions = $allSubscriptions->where('status', 'cancelled');
        $trialSubscriptions = $allSubscriptions->whereIn('status', ['trial', 'trialing']);

        // Revenue calculations
        $mrr = $this->monthlyRevenueEstimate($allSubscriptions);
        $arr = $mrr * 12;

        // Churn & Renewal calculations based on History
        // Simplified approach based on current state
        $totalEverActive = $allSubscriptions->whereIn('status', ['active', 'grace', 'expired', 'suspended', 'cancelled'])->count();
        $churnRate = $totalEverActive > 0 ? round((($cancelledSubscriptions->count() + $suspendedSubscriptions->count()) / $totalEverActive) * 100, 2) : 0;
        
        // Renewal Rate: Renewed vs (Renewed + Cancelled) in a real app, here simplified
        $renewedCount = \App\Models\SubscriptionHistory::where('action', 'renewed')->count();
        $cancelledCount = \App\Models\SubscriptionHistory::where('action', 'cancelled')->count();
        $renewalRate = ($renewedCount + $cancelledCount) > 0 ? round(($renewedCount / ($renewedCount + $cancelledCount)) * 100, 2) : 100;

        return [
            'total_tenants' => $totalTenants,
            'license_status' => $licenseStatus,
            'is_trial' => $isTrial,
            'is_suspended' => $isSuspended,
            'expiring_in_7_days' => $expiringSoonCount,
            'grace_period' => $graceSubscriptions->count(),
            'expired' => $expiredSubscriptions->count(),
            'suspended' => $suspendedSubscriptions->count(),
            'total_users' => User::withoutGlobalScopes()->count(),
            'total_students' => Student::withoutGlobalScopes()->count(),
            'total_plans' => Plan::count(),
            'active_subscriptions' => $activeSubscriptions->count(),
            'trial_tenants' => $trialSubscriptions->count(),
            'monthly_revenue_estimate' => $mrr,
            'mrr' => $mrr,
            'arr' => $arr,
            'churn_rate' => $churnRate,
            'renewal_rate' => $renewalRate,
        ];
    }

    protected function monthlyRevenueEstimate(Collection $subscriptions): float
    {
        return $subscriptions->sum(function (Subscription $subscription) {
            $plan = $subscription->plan;

            if (!$plan || !in_array($subscription->status, ['active', 'trial'], true)) {
                return 0;
            }

            $billingCycle = $plan->billing_cycle ?: $plan->billing_period;

            return $billingCycle === 'yearly'
                ? ((float) $subscription->price / 12)
                : (float) $subscription->price;
        });
    }
}
