<?php

namespace App\Domain\HQ\Services\Observability;

use App\Jobs\ProcessObservabilityMetricJob;

class HQMetricService
{
    public function recordMetric(string $name, string $type, float $value, ?string $unit = null, array $tags = [], ?int $tenantId = null): void
    {
        $metricData = [
            'tenant_id' => $tenantId,
            'metric_name' => $name,
            'metric_type' => $type,
            'value' => $value,
            'unit' => $unit,
            'tags' => $tags,
        ];

        ProcessObservabilityMetricJob::dispatch($metricData);
    }

    public function increment(string $name, array $tags = [], ?int $tenantId = null): void
    {
        $this->recordMetric($name, 'counter', 1.0, 'count', $tags, $tenantId);
    }

    public function gauge(string $name, float $value, ?string $unit = null, array $tags = [], ?int $tenantId = null): void
    {
        $this->recordMetric($name, 'gauge', $value, $unit, $tags, $tenantId);
    }

    public function timing(string $name, float $ms, array $tags = [], ?int $tenantId = null): void
    {
        $this->recordMetric($name, 'timing', $ms, 'ms', $tags, $tenantId);
    }
}
