<?php

namespace App\Jobs;

use App\Domain\HQ\Services\Observability\ObservabilityRetentionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanUpObservabilityDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $service = app(ObservabilityRetentionService::class);
        
        $service->cleanLogs(30);
        $service->cleanRawMetrics(7);
        $service->cleanTraces(15);
        $service->cleanSecurityEvents(90);
    }
}
