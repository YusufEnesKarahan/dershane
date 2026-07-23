<?php

namespace App\Domain\Reporting\Services;

use App\Core\Repositories\Interfaces\ReportingRepositoryInterface;
use App\DTOs\Reporting\ReportScheduleDTO;
use App\DTOs\Reporting\ReportExportDTO;
use App\Models\ReportExport;
use App\Models\ReportSchedule;
use App\Models\ExecutiveReport;
use Illuminate\Support\Facades\Storage;

class ReportingService
{
    public function __construct(protected ReportingRepositoryInterface $repo) {}

    public function getSchedules(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getSchedules();
    }

    public function createSchedule(ReportScheduleDTO $dto): ReportSchedule
    {
        return $this->repo->createSchedule($dto);
    }

    public function getExports(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getExports();
    }

    public function createExport(ReportExportDTO $dto): ReportExport
    {
        return $this->repo->createExport($dto);
    }

    public function generateExportFile(int $exportId): string
    {
        $export = ReportExport::findOrFail($exportId);
        $fileName = 'report_' . $export->id . '_' . time() . '.' . strtolower($export->format);
        $fileDir = 'reports/';
        
        // Mock report file contents
        $content = "Dershane Yonetici Raporu\n";
        $content .= "Rapor Tipi: " . $export->report_type . "\n";
        $content .= "Olusturulma Tarihi: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Format: " . $export->format . "\n";

        Storage::disk('local')->put($fileDir . $fileName, $content);
        $filePath = Storage::path($fileDir . $fileName);

        $this->repo->updateExportStatus($exportId, 'Completed', $filePath);

        return $filePath;
    }

    public function getExecutiveReports(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->getExecutiveReports();
    }

    public function createExecutiveReport(string $title, ?string $description, array $contentData): ExecutiveReport
    {
        return $this->repo->createExecutiveReport($title, $description, $contentData);
    }
}
