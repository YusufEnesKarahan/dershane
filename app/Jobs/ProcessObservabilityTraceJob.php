<?php

namespace App\Jobs;

use App\Models\HQTrace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessObservabilityTraceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $traceData;

    public function __construct(array $traceData)
    {
        $this->traceData = $traceData;
    }

    public function handle(): void
    {
        HQTrace::create($this->traceData);
    }
}
