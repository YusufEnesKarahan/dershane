<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\DashboardSnapshot;
use App\Models\ReportExport;
use App\Models\ReportSchedule;
use App\Models\ExecutiveReport;
use App\DTOs\Reporting\ReportScheduleDTO;
use App\DTOs\Reporting\ReportExportDTO;

interface ReportingRepositoryInterface
{
    public function getLatestSnapshot(): ?DashboardSnapshot;

    public function createSnapshot(array $metrics): DashboardSnapshot;

    public function getSchedules(): \Illuminate\Database\Eloquent\Collection;

    public function createSchedule(ReportScheduleDTO $dto): ReportSchedule;

    public function getExports(): \Illuminate\Database\Eloquent\Collection;

    public function createExport(ReportExportDTO $dto): ReportExport;

    public function updateExportStatus(int $id, string $status, ?string $filePath = null): bool;

    public function createExecutiveReport(string $title, ?string $description, array $contentData): ExecutiveReport;

    public function getExecutiveReports(): \Illuminate\Database\Eloquent\Collection;
}
