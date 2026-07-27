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

    public function __construct(public readonly int $exportId) {}

    public function handle(ReportingService $reportingService): void
    {
        try {
            $reportingService->generateExportFile($this->exportId);
        } catch (\Throwable $e) {
            Log::error('ExportReportJob failed: ' . $e->getMessage());
            $export = ReportExport::find($this->exportId);
            if ($export) {
                $export->update(['status' => 'Failed']);
            }
            throw $e;
        }
    }
}
