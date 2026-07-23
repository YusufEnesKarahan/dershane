<?php

namespace App\Domain\Reporting\Actions;

use App\DTOs\Reporting\ReportScheduleDTO;
use App\Domain\Reporting\Services\ReportingService;
use App\Models\ReportSchedule;

class ScheduleReport
{
    public function __construct(protected ReportingService $service) {}

    public function execute(ReportScheduleDTO $dto): ReportSchedule
    {
        return $this->service->createSchedule($dto);
    }
}
