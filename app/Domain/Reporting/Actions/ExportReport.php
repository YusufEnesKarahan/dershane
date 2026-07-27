<?php

namespace App\Domain\Reporting\Actions;

use App\DTOs\Reporting\ReportExportDTO;
use App\Domain\Reporting\Services\ReportingService;
use App\Models\ReportExport;

class ExportReport
{
    public function __construct(protected ReportingService $service) {}

    public function execute(ReportExportDTO $dto): ReportExport
    {
        $export = $this->service->createExport($dto);
        \App\Jobs\ExportReportJob::dispatch($export->id);
        return $export;
    }
}
