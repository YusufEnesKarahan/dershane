<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Reporting\Services\ExecutiveDashboardService;
use App\Domain\Reporting\Actions\GenerateDashboardSnapshot;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    public function __construct(
        protected ExecutiveDashboardService $dashboardService,
        protected GenerateDashboardSnapshot $snapshotAction,
        protected \App\Domain\Platform\Services\LicenseService $licenseService,
        protected \App\Domain\Platform\Services\FeatureFlagService $featureFlagService,
        protected \App\Domain\Platform\Services\UpdateService $updateService,
        protected \App\Domain\Platform\Services\HQIntegrationService $hqIntegrationService
    ) {}

    public function index()
    {
        $metrics = $this->dashboardService->getMetrics();
        $metrics['active_users'] = \App\Models\User::count();
        $metrics['active_features'] = $this->featureFlagService->getAllFlags()->where('enabled', true)->count();
        $metrics['license_status'] = $this->licenseService->checkLicense()['status'] ?? 'Yok';
        
        $metrics['update_status'] = [
            'current_version' => $this->updateService->currentVersion(),
            'latest_version' => $this->updateService->getLatest()?->version ?? $this->updateService->currentVersion(),
            'is_available' => $this->updateService->isUpdateAvailable(),
        ];
        
        $metrics['hq_status'] = [
            'connected' => false,
            'system_uuid' => $this->hqIntegrationService->getInstanceInformation()->uuid,
            'license' => $this->hqIntegrationService->getLicenseStatus()['status'] ?? 'Unknown',
            'version' => $this->hqIntegrationService->getSystemVersion(),
        ];

        return view('admin.reporting.dashboard', compact('metrics'));
    }

    public function analytics()
    {
        $metrics = $this->dashboardService->getMetrics();
        return view('admin.reporting.analytics', compact('metrics'));
    }

    public function snapshot()
    {
        $this->snapshotAction->execute();
        return redirect()->back()->with('success', 'Dashboard anlık görüntüsü (snapshot) başarıyla kaydedildi.');
    }
}
