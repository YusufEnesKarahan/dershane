<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\SubscriptionLimitService;
use App\Domain\Platform\Services\SaaSOperationsService;
use App\Domain\Platform\Services\SystemHealthService;
use App\Models\Branch;
use App\Models\PlatformAuditLog;
use Illuminate\Http\Request;

class SaaSTenantController extends Controller
{
    public function __construct(
        protected SaaSOperationsService $saasService,
        protected SystemHealthService $systemHealthService,
        protected SubscriptionLimitService $subscriptionLimitService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $tenants = $this->saasService->getAllTenants($search);
        $license = $this->saasService->getSystemLicense();
        
        return view('admin.saas.tenants.index', compact('tenants', 'license'));
    }

    public function show(Branch $tenant)
    {
        $stats = $this->saasService->getTenantUsageStats($tenant->id);
        $license = $this->saasService->getSystemLicense();
        $systemHealth = $this->systemHealthService->getTenantHealthSummary();
        $tenantActivities = $this->saasService->getTenantActivityFeed($tenant->id);
        $tenantSubscription = $tenant->subscription()->with('plan')->first();
        $tenantPlan = $tenantSubscription?->plan;

        $subscriptionUsage = [
            'students' => [
                'current' => $stats['students_count'] ?? 0,
                'limit' => $this->subscriptionLimitService->limit('max_students', $tenant),
            ],
            'teachers' => [
                'current' => $stats['teachers_count'] ?? 0,
                'limit' => $this->subscriptionLimitService->limit('max_teachers', $tenant),
            ],
            'users' => [
                'current' => $stats['users_count'] ?? 0,
                'limit' => $this->subscriptionLimitService->limit('max_users', $tenant),
            ],
        ];

        foreach ($subscriptionUsage as $key => $usage) {
            $limit = $usage['limit'];
            $current = $usage['current'];
            $subscriptionUsage[$key]['percent'] = $limit ? min(100, (int) round(($current / max(1, $limit)) * 100)) : null;
        }
        
        $subscriptionHistory = $license && $license->subscription
            ? $license->subscription->histories()->latest()->take(10)->get()
            : collect();
            
        return view('admin.saas.tenants.show', compact('tenant', 'stats', 'license', 'systemHealth', 'tenantActivities', 'subscriptionHistory', 'tenantSubscription', 'tenantPlan', 'subscriptionUsage'));
    }

    public function suspend(Branch $tenant)
    {
        if ($this->saasService->suspendLicense()) {
            PlatformAuditLog::record(auth()->user(), 'tenant.suspended', $tenant, [
                'description' => 'Tenant Super Admin tarafından askıya alındı.',
            ]);

            return redirect()->back()->with('success', 'Sistem lisansı askıya alındı.');
        }
        
        return redirect()->back()->with('error', 'Lisans bulunamadı veya askıya alınamadı.');
    }

    public function activate(Branch $tenant)
    {
        if ($this->saasService->activateLicense()) {
            PlatformAuditLog::record(auth()->user(), 'tenant.activated', $tenant, [
                'description' => 'Tenant Super Admin tarafından yeniden aktif edildi.',
            ]);

            return redirect()->back()->with('success', 'Sistem lisansı aktifleştirildi.');
        }
        
        return redirect()->back()->with('error', 'Lisans bulunamadı veya aktifleştirilemedi.');
    }
}
