<?php

namespace App\Domain\HQ\Services\Observability;

use App\Models\HQLog;
use App\Models\HQMetric;
use App\Models\HQTrace;
use App\Models\HQSecurityEvent;
use Illuminate\Support\Carbon;

class ObservabilityRetentionService
{
    /**
     * Delete logs older than retention days.
     * Often retention days come from Entitlement/Plan features, 
     * but here we allow a fallback or standard default.
     */
    public function cleanLogs(int $days = 30): void
    {
        HQLog::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Delete raw metrics after aggregation. Typically raw metrics are heavy 
     * and should only be kept for 7 days.
     */
    public function cleanRawMetrics(int $days = 7): void
    {
        HQMetric::where('recorded_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Delete traces older than retention days.
     */
    public function cleanTraces(int $days = 15): void
    {
        HQTrace::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Delete security events older than retention days.
     */
    public function cleanSecurityEvents(int $days = 90): void
    {
        HQSecurityEvent::where('created_at', '<', now()->subDays($days))->delete();
    }
}
