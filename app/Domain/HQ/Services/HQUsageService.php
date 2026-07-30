<?php

namespace App\Domain\HQ\Services;

use App\Models\HQTenant;
use App\Models\HQUsageMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class HQUsageService
{
    public function __construct(
        protected QuotaEvaluationService $quotaEvaluationService
    ) {}

    /**
     * Record a batch of metrics for a tenant.
     */
    public function recordMetrics(HQTenant $tenant, array $metrics, ?Carbon $reportedAt = null): void
    {
        $reportedAt = $reportedAt ?? now();
        $records = [];
        $cacheKeys = [];

        foreach ($metrics as $key => $value) {
            $records[] = [
                'tenant_id' => $tenant->id,
                'metric_key' => $key,
                'metric_value' => $value,
                'reported_at' => $reportedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Invalidate current usage cache for this metric
            $cacheKeys[] = $this->getCacheKey($tenant, $key);
        }

        if (!empty($records)) {
            DB::transaction(function () use ($records, $cacheKeys, $tenant, $metrics) {
                // Insert raw metrics
                HQUsageMetric::insert($records);
                
                // Clear cache
                foreach ($cacheKeys as $key) {
                    Cache::forget($key);
                }

                // Evaluate quotas asynchronously or synchronously depending on scale.
                // We'll do it synchronously here inside transaction to ensure consistency.
                $this->quotaEvaluationService->evaluate($tenant, $metrics);
            });
        }
    }

    /**
     * Get the latest usage value for a specific metric.
     */
    public function getLatestMetric(HQTenant $tenant, string $metricKey): float
    {
        return Cache::remember($this->getCacheKey($tenant, $metricKey), now()->addMinutes(15), function () use ($tenant, $metricKey) {
            $latest = HQUsageMetric::where('tenant_id', $tenant->id)
                ->where('metric_key', $metricKey)
                ->latest('reported_at')
                ->first();
                
            return $latest ? (float) $latest->metric_value : 0.0;
        });
    }

    /**
     * Generate standard cache key for usage.
     */
    protected function getCacheKey(HQTenant $tenant, string $metricKey): string
    {
        return "hq_usage:tenant_{$tenant->id}:metric_{$metricKey}";
    }
}
