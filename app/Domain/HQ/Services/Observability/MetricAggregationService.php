<?php

namespace App\Domain\HQ\Services\Observability;

use App\Models\HQMetric;
use App\Models\HQMetricSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MetricAggregationService
{
    public function aggregateHourly(Carbon $date = null): void
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfHour();
        $end = $date->copy()->endOfHour();

        $this->performAggregation('hourly', $start, $end);
    }

    public function aggregateDaily(Carbon $date = null): void
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $this->performAggregation('daily', $start, $end);
    }

    public function aggregateMonthly(Carbon $date = null): void
    {
        $date = $date ?? now();
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $this->performAggregation('monthly', $start, $end);
    }

    protected function performAggregation(string $period, Carbon $start, Carbon $end): void
    {
        // We aggregate by metric_name and type (counter => sum, gauge => avg, max, min)
        $metrics = HQMetric::whereBetween('recorded_at', [$start, $end])
            ->select(
                'metric_name',
                'metric_type',
                DB::raw('COUNT(value) as count_val'),
                DB::raw('SUM(value) as sum_val'),
                DB::raw('AVG(value) as avg_val'),
                DB::raw('MAX(value) as max_val'),
                DB::raw('MIN(value) as min_val')
            )
            ->groupBy('metric_name', 'metric_type')
            ->get();

        foreach ($metrics as $metric) {
            $dateString = $start->toDateTimeString();

            if ($metric->metric_type === 'counter') {
                $this->upsertSnapshot($metric->metric_name, 'sum', $metric->sum_val, $period, $dateString);
            } elseif ($metric->metric_type === 'gauge' || $metric->metric_type === 'timing') {
                $this->upsertSnapshot($metric->metric_name, 'avg', $metric->avg_val, $period, $dateString);
                $this->upsertSnapshot($metric->metric_name, 'max', $metric->max_val, $period, $dateString);
                $this->upsertSnapshot($metric->metric_name, 'min', $metric->min_val, $period, $dateString);
            }
        }
    }

    protected function upsertSnapshot(string $name, string $aggregationType, float $value, string $period, string $date): void
    {
        HQMetricSnapshot::updateOrCreate(
            [
                'metric_name' => $name,
                'aggregation_type' => $aggregationType,
                'period' => $period,
                'snapshot_date' => $date,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
