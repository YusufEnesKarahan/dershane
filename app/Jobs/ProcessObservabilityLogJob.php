<?php

namespace App\Jobs;

use App\Models\HQLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessObservabilityLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $logData;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    public function handle(): void
    {
        HQLog::create($this->logData);
    }
}
