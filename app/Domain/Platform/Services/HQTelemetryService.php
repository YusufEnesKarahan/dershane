<?php

namespace App\Domain\Platform\Services;

use App\Models\HQTelemetryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HQTelemetryService
{
    public function __construct(
        protected HQIntegrationService $hqIntegrationService,
        protected UpdateService $updateService,
        protected FeatureFlagService $featureFlagService
    ) {}

    public function collectHealth(): array
    {
        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Exception $e) {
        }

        $storageWritable = is_writable(storage_path());

        $cacheStatus = false;
        try {
            Cache::put('telemetry_health_check', true, 1);
            $cacheStatus = Cache::has('telemetry_health_check');
        } catch (\Exception $e) {
        }

        return [
            'status' => ($dbConnected && $storageWritable && $cacheStatus) ? 'healthy' : 'degraded',
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'database_connected' => $dbConnected,
            'storage_writable' => $storageWritable,
            'cache_status' => $cacheStatus,
        ];
    }

    public function collectSystem(): array
    {
        $identity = $this->hqIntegrationService->getInstanceInformation();
        
        return [
            'system_uuid' => $identity->uuid ?? 'UNKNOWN',
            'installation_uuid' => $identity->installation_uuid ?? 'UNKNOWN',
            'app_version' => $this->updateService->currentVersion(),
            'environment' => app()->environment(),
            'timezone' => config('app.timezone'),
        ];
    }

    public function collectUsage(): array
    {
        return [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'active_branches' => \App\Models\Branch::where('is_active', true)->count(),
            'active_features' => $this->featureFlagService->getAllFlags()->where('enabled', true)->count(),
        ];
    }

    public function collectPerformance(): array
    {
        $memoryUsage = memory_get_usage(true);
        $peakMemory = memory_get_peak_usage(true);
        $diskFree = disk_free_space(storage_path());
        $diskTotal = disk_total_space(storage_path());
        
        return [
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'peak_memory_mb' => round($peakMemory / 1024 / 1024, 2),
            'disk_usage_percent' => $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 2) : 0,
            'response_metrics' => 'N/A', // Read-only summary, mock placeholder
        ];
    }

    public function createSnapshot(): array
    {
        return [
            'system' => $this->collectSystem(),
            'health' => $this->collectHealth(),
            'usage' => $this->collectUsage(),
            'performance' => $this->collectPerformance(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function storeSnapshot(array $snapshot, string $type = 'snapshot', string $status = 'success'): HQTelemetryLog
    {
        return HQTelemetryLog::create([
            'type' => $type,
            'payload' => $snapshot,
            'status' => $status,
            'generated_at' => now(),
        ]);
    }
}
