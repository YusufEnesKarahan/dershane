<?php

namespace App\Jobs;

use App\Models\HQMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessObservabilityMetricJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $metricData;

    public function __construct(array $metricData)
    {
        $this->metricData = $metricData;
    }

    public function handle(): void
    {
        HQMetric::create($this->metricData);
    }
}
