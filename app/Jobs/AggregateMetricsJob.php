<?php

namespace App\Jobs;

use App\Domain\HQ\Services\Observability\MetricAggregationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AggregateMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $period;

    public function __construct(string $period = 'hourly')
    {
        $this->period = $period;
    }

    public function handle(): void
    {
        $service = app(MetricAggregationService::class);
        
        if ($this->period === 'hourly') {
            $service->aggregateHourly(now()->subHour()); // Aggregate previous hour
        } elseif ($this->period === 'daily') {
            $service->aggregateDaily(now()->subDay()); // Aggregate previous day
        } elseif ($this->period === 'monthly') {
            $service->aggregateMonthly(now()->subMonth()); // Aggregate previous month
        }
    }
}
