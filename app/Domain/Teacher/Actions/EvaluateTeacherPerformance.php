<?php

namespace App\Domain\Teacher\Actions;

use App\DTOs\Teacher\TeacherPerformanceDTO;
use App\Domain\Teacher\Services\TeacherPerformanceService;
use App\Models\TeacherPerformanceLog;

class EvaluateTeacherPerformance
{
    public function __construct(protected TeacherPerformanceService $service) {}

    public function execute(TeacherPerformanceDTO $dto): TeacherPerformanceLog
    {
        return $this->service->logPerformance($dto);
    }
}
