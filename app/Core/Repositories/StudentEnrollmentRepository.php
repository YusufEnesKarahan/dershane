<?php
namespace App\Core\Repositories;

use App\Models\StudentEnrollment;
use App\Core\Repositories\Interfaces\StudentEnrollmentRepositoryInterface;
use Illuminate\Support\Collection;

class StudentEnrollmentRepository implements StudentEnrollmentRepositoryInterface
{
    public function getByStudent(int $studentId): Collection
    {
        return StudentEnrollment::with(['course', 'term'])->where('student_id', $studentId)->get();
    }

    public function create(array $data): StudentEnrollment
    {
        return StudentEnrollment::create($data);
    }
}
