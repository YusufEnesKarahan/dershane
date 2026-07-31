<?php

namespace App\Events;

use App\Models\HQMetric;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MetricThresholdExceeded
{
    use Dispatchable, SerializesModels;

    public $metric;
    public $threshold;

    public function __construct(HQMetric $metric, float $threshold)
    {
        $this->metric = $metric;
        $this->threshold = $threshold;
    }
}
