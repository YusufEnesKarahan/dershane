<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\ReportingRepositoryInterface;
use App\Models\DashboardSnapshot;
use App\Models\ReportExport;
use App\Models\ReportSchedule;
use App\Models\ExecutiveReport;
use App\DTOs\Reporting\ReportScheduleDTO;
use App\DTOs\Reporting\ReportExportDTO;

class ReportingRepository implements ReportingRepositoryInterface
{
    public function getLatestSnapshot(): ?DashboardSnapshot
    {
        return DashboardSnapshot::orderBy('created_at', 'desc')->first();
    }

    public function createSnapshot(array $metrics): DashboardSnapshot
    {
        return DashboardSnapshot::create(['metrics' => $metrics]);
    }

    public function getSchedules(): \Illuminate\Database\Eloquent\Collection
    {
        return ReportSchedule::orderBy('created_at', 'desc')->get();
    }

    public function createSchedule(ReportScheduleDTO $dto): ReportSchedule
    {
        return ReportSchedule::create($dto->toArray());
    }

    public function getExports(): \Illuminate\Database\Eloquent\Collection
    {
        return ReportExport::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function createExport(ReportExportDTO $dto): ReportExport
    {
        return ReportExport::create($dto->toArray());
    }

    public function updateExportStatus(int $id, string $status, ?string $filePath = null): bool
    {
        $export = ReportExport::findOrFail($id);
        $data = ['status' => $status];
        if ($filePath) {
            $data['file_path'] = $filePath;
        }
        return $export->update($data);
    }

    public function createExecutiveReport(string $title, ?string $description, array $contentData): ExecutiveReport
    {
        return ExecutiveReport::create([
            'title' => $title,
            'description' => $description,
            'content_data' => $contentData,
        ]);
    }

    public function getExecutiveReports(): \Illuminate\Database\Eloquent\Collection
    {
        return ExecutiveReport::orderBy('created_at', 'desc')->get();
    }
}
