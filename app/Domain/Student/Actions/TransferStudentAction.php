<?php
namespace App\Domain\Student\Actions;

use App\Domain\Student\Services\StudentService;
use App\Models\Student;

class TransferStudentAction
{
    public function __construct(protected StudentService $service) {}

    public function execute(Student $student, int $toBranchId, ?int $toClassroomId, ?string $reason): void
    {
        $this->service->transfer($student, $toBranchId, $toClassroomId, $reason);
    }
}
