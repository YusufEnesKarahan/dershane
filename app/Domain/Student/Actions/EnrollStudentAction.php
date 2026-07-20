<?php
namespace App\Domain\Student\Actions;

use App\DTOs\Student\StudentEnrollmentDTO;
use App\Domain\Student\Services\EnrollmentService;
use App\Models\StudentEnrollment;

class EnrollStudentAction
{
    public function __construct(protected EnrollmentService $service) {}

    public function execute(StudentEnrollmentDTO $dto): StudentEnrollment
    {
        return $this->service->enroll($dto);
    }
}
