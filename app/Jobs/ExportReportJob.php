<?php

namespace App\Jobs;

use App\Domain\Reporting\Services\ReportingService;
use App\Models\ReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $backoff = 10;

    public function __construct(public readonly int $exportId)
    {
        $this->onQueue('reports');
    }

    public function handle(ReportingService $reportingService): void
    {
        $reportingService->generateExportFile($this->exportId);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ExportReportJob failed: ' . $exception->getMessage());
        $export = ReportExport::find($this->exportId);
        if ($export) {
            $export->update(['status' => 'Failed']);
        }
    }
}
