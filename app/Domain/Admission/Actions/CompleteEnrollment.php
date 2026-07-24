<?php

namespace App\Domain\Admission\Actions;

use App\DTOs\Admission\EnrollStudentDTO;
use App\Domain\Admission\Services\EnrollmentService;
use App\Models\StudentEnrollment;

class CompleteEnrollment
{
    public function __construct(protected EnrollmentService $service) {}

    public function execute(EnrollStudentDTO $dto, ?int $userId = null): StudentEnrollment
    {
        return $this->service->enrollStudent($dto, $userId);
    }
}
