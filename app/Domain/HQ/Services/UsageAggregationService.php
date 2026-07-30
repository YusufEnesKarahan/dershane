<?php

namespace App\Domain\HQ\Services;

use App\Models\HQTenant;
use App\Models\HQUsageMetric;
use App\Models\HQUsageSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsageAggregationService
{
    /**
     * Aggregate raw metrics into snapshots for a given period.
     */
    public function aggregate(string $period = 'hourly'): void
    {
        $now = now();
        
        [$periodStart, $periodEnd] = match ($period) {
            'hourly' => [$now->copy()->subHour()->startOfHour(), $now->copy()->subHour()->endOfHour()],
            'daily' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'weekly' => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'monthly' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            default => throw new \InvalidArgumentException("Invalid aggregation period: {$period}")
        };

        // If daily/weekly/monthly, we can aggregate from the hourly snapshots rather than raw metrics for performance.
        // But for simplicity and zero-RCE requirements, we'll aggregate from raw if it's hourly, 
        // and from hourly snapshots if it's daily, etc.

        if ($period === 'hourly') {
            $this->aggregateRawToHourly($periodStart, $periodEnd);
        } else {
            $this->aggregateSnapshotsToHigherPeriod($period, $periodStart, $periodEnd);
        }
    }

    protected function aggregateRawToHourly(Carbon $start, Carbon $end): void
    {
        // Get maximum value for each metric in the hour for each tenant
        $aggregated = HQUsageMetric::select('tenant_id', 'metric_key', DB::raw('MAX(metric_value) as max_value'))
            ->whereBetween('reported_at', [$start, $end])
            ->groupBy('tenant_id', 'metric_key')
            ->get();

        $snapshots = [];
        
        foreach ($aggregated->groupBy('tenant_id') as $tenantId => $metrics) {
            $dataJson = [];
            foreach ($metrics as $m) {
                $dataJson[$m->metric_key] = (float) $m->max_value;
            }

            $snapshots[] = [
                'tenant_id' => $tenantId,
                'period' => 'hourly',
                'period_start' => $start,
                'period_end' => $end,
                'data_json' => json_encode($dataJson),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($snapshots)) {
            HQUsageSnapshot::insert($snapshots);
            
            // Clean up raw metrics older than 7 days to keep DB size manageable
            HQUsageMetric::where('reported_at', '<', now()->subDays(7))->delete();
        }
    }

    protected function aggregateSnapshotsToHigherPeriod(string $period, Carbon $start, Carbon $end): void
    {
        $basePeriod = match($period) {
            'daily' => 'hourly',
            'weekly' => 'daily',
            'monthly' => 'daily', // Monthly built from daily
        };

        $baseSnapshots = HQUsageSnapshot::where('period', $basePeriod)
            ->where('period_start', '>=', $start)
            ->where('period_end', '<=', $end)
            ->get();

        $snapshots = [];

        foreach ($baseSnapshots->groupBy('tenant_id') as $tenantId => $records) {
            $mergedData = [];
            
            // We'll take the maximum value seen in the period for resource-based metrics (like users, storage)
            foreach ($records as $record) {
                $data = $record->data_json;
                foreach ($data as $key => $val) {
                    if (!isset($mergedData[$key])) {
                        $mergedData[$key] = $val;
                    } else {
                        // Max is usually safe for storage, users, etc. 
                        // If we had cumulative metrics (like API calls), we'd sum them.
                        // Assuming simple max peak for this scope.
                        $mergedData[$key] = max($mergedData[$key], $val); 
                    }
                }
            }

            $snapshots[] = [
                'tenant_id' => $tenantId,
                'period' => $period,
                'period_start' => $start,
                'period_end' => $end,
                'data_json' => json_encode($mergedData),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($snapshots)) {
            HQUsageSnapshot::insert($snapshots);
        }
    }
}
