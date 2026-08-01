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
        protected \App\Domain\Platform\Services\HQIntegrationService $hqIntegrationService,
        protected \App\Domain\Platform\Services\HQApiService $hqApiService,
        protected \App\Domain\Platform\Services\HQSyncService $hqSyncService
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

        $activeToken = $this->hqApiService->getActiveToken();
        $metrics['hq_api_status'] = [
            'has_token' => $activeToken !== null,
            'token_name' => $activeToken?->name ?? 'Yok',
            'expires_at' => $activeToken?->expires_at ? $activeToken->expires_at->format('d M Y') : 'Sınırsız',
        ];
        
        $metrics['hq_sync_status'] = [
            'pending' => $this->hqSyncService->pending(),
            'completed' => $this->hqSyncService->completed(),
            'failed' => $this->hqSyncService->failed(),
            'last_event' => \App\Models\HQSyncEvent::latest()->first()?->created_at->format('d M H:i') ?? 'Yok',
        ];

        $lastPingLog = \App\Models\HQSyncLog::where('event_type', 'ping')->where('success', true)->latest('created_at')->first();
        $lastSyncLog = \App\Models\HQSyncLog::where('event_type', 'sync')->where('success', true)->latest('created_at')->first();
        
        $metrics['hq_connection'] = [
            'connected' => $lastPingLog && $lastPingLog->created_at->diffInHours(now()) < 24,
            'last_ping' => $lastPingLog?->created_at->format('d M H:i') ?? 'Yok',
            'last_sync' => $lastSyncLog?->created_at->format('d M H:i') ?? 'Yok',
        ];

        $metrics['hq_commands'] = [
            'pending' => \App\Models\HQCommand::where('status', 'pending')->count(),
            'failed' => \App\Models\HQCommand::where('status', 'failed')->count(),
            'last_execution' => \App\Models\HQCommand::whereNotNull('executed_at')->latest('executed_at')->first()?->executed_at->format('d M H:i') ?? 'Yok',
        ];

        $lastTelemetry = \App\Models\HQTelemetryLog::latest('generated_at')->first();
        $metrics['hq_telemetry_status'] = [
            'last_snapshot' => $lastTelemetry?->generated_at->format('d M H:i') ?? 'Yok',
            'health_status' => $lastTelemetry ? (isset($lastTelemetry->payload['health']['status']) ? strtoupper($lastTelemetry->payload['health']['status']) : 'UNKNOWN') : 'N/A',
            'failed_count' => \App\Models\HQTelemetryLog::where('status', 'failed')->count(),
        ];

        $metrics['hq_scheduler_status'] = [
            'enabled' => config('hq.scheduler.enabled'),
            'last_success' => \App\Models\HQSchedulerLog::where('status', 'success')->latest('finished_at')->first()?->finished_at->format('d M H:i') ?? 'Yok',
            'failed_count' => \App\Models\HQSchedulerLog::where('status', 'failed')->count(),
        ];

        $latestUpdate = \App\Models\HQUpdate::orderBy('created_at', 'desc')->first();
        $metrics['hq_update_status'] = [
            'current_version' => config('app.version', '1.0.0'),
            'latest_version' => $latestUpdate ? $latestUpdate->version : 'None',
            'pending_count' => \App\Models\HQUpdate::where('status', 'available')->count(),
        ];
        
        $metrics['hq_central_status'] = app(\App\Core\Services\HQMonitoringService::class)->getDashboardMetrics();
        
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
