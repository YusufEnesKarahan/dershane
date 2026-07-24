<?php

namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Core\Repositories\Interfaces\TeacherPortalRepositoryInterface;
use Illuminate\Support\Collection;

class TeacherPortalService
{
    public function __construct(protected TeacherPortalRepositoryInterface $repository) {}

    public function getTeacherByUserId(int $userId): ?Teacher
    {
        return $this->repository->findByUserId($userId);
    }

    public function getAssignedClasses(int $teacherId): Collection
    {
        return $this->repository->getAssignedClasses($teacherId);
    }

    public function canManageClassCourse(int $teacherId, int $classroomId, int $courseId): bool
    {
        return $this->repository->hasAssignment($teacherId, $classroomId, $courseId);
    }

    public function getClassRoster(int $classroomId): Collection
    {
        return Student::where('classroom_id', $classroomId)->get();
    }
}
