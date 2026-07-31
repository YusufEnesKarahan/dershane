<?php

namespace App\Domain\HQ\Services\Observability;

use App\Models\HQHealthCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthMonitoringService
{
    public function checkAll(?int $tenantId = null): array
    {
        $results = [
            'database' => $this->checkDatabase($tenantId),
            'cache' => $this->checkCache($tenantId),
            'storage' => $this->checkStorage($tenantId),
            // Queue could be checked by seeing if jobs are piling up, or just basic ping
        ];

        return $results;
    }

    public function checkDatabase(?int $tenantId = null): HQHealthCheck
    {
        $start = microtime(true);
        $status = 'healthy';
        $details = [];

        try {
            DB::connection()->getPdo();
            $details['message'] = 'Connection successful';
        } catch (Throwable $e) {
            $status = 'critical';
            $details['error'] = $e->getMessage();
        }

        $responseTime = (int) ((microtime(true) - $start) * 1000);

        if ($status === 'healthy' && $responseTime > 1000) {
            $status = 'warning';
        }

        return $this->recordCheck('database', $status, $responseTime, $details, $tenantId);
    }

    public function checkCache(?int $tenantId = null): HQHealthCheck
    {
        $start = microtime(true);
        $status = 'healthy';
        $details = [];

        try {
            Cache::store()->set('health_check', 'ok', 1);
            if (Cache::store()->get('health_check') !== 'ok') {
                throw new \Exception('Cache read/write failed');
            }
        } catch (Throwable $e) {
            $status = 'critical';
            $details['error'] = $e->getMessage();
        }

        $responseTime = (int) ((microtime(true) - $start) * 1000);

        if ($status === 'healthy' && $responseTime > 500) {
            $status = 'warning';
        }

        return $this->recordCheck('cache', $status, $responseTime, $details, $tenantId);
    }

    public function checkStorage(?int $tenantId = null): HQHealthCheck
    {
        $start = microtime(true);
        $status = 'healthy';
        $details = [];

        try {
            $disk = Storage::disk('local');
            $disk->put('health_check.txt', 'ok');
            if (!$disk->exists('health_check.txt')) {
                throw new \Exception('Storage write failed');
            }
            $disk->delete('health_check.txt');
        } catch (Throwable $e) {
            $status = 'critical';
            $details['error'] = $e->getMessage();
        }

        $responseTime = (int) ((microtime(true) - $start) * 1000);

        if ($status === 'healthy' && $responseTime > 2000) {
            $status = 'warning';
        }

        return $this->recordCheck('storage', $status, $responseTime, $details, $tenantId);
    }

    protected function recordCheck(string $component, string $status, int $responseTime, array $details, ?int $tenantId): HQHealthCheck
    {
        $healthCheck = HQHealthCheck::create([
            'tenant_id' => $tenantId,
            'component' => $component,
            'status' => $status,
            'response_time' => $responseTime,
            'details' => $details,
        ]);

        if ($status !== 'healthy') {
            app(HQMetricService::class)->increment('health.check.failed', ['component' => $component], $tenantId);
        }

        return $healthCheck;
    }
}
