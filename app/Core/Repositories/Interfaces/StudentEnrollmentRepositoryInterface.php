<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\StudentEnrollment;
use Illuminate\Support\Collection;

interface StudentEnrollmentRepositoryInterface
{
    public function getByStudent(int $studentId): Collection;
    public function create(array $data): StudentEnrollment;
}
