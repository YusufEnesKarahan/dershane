<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\StudentEnrollment;
use App\DTOs\Admission\EnrollStudentDTO;

interface EnrollmentRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function find(int $id): ?StudentEnrollment;

    public function create(EnrollStudentDTO $dto, int $studentId, ?int $invoiceId = null): StudentEnrollment;
}
