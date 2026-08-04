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

    public function getWeeklySchedule(int $branchId, int $teacherId, int $academicTermId)
    {
        return \App\Models\LessonSchedule::where('branch_id', $branchId)
            ->where('academic_term_id', $academicTermId)
            ->where(function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhereHas('additionalTeachers', function($q) use ($teacherId) {
                          $q->where('teacher_id', $teacherId);
                      });
            })
            ->with(['course', 'classroom'])
            ->get();
    }

    public function getPendingHomeworkReviews(int $teacherId)
    {
        return \App\Models\HomeworkSubmission::whereHas('homework', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->whereIn('status', ['submitted', 'late'])
            ->with(['homework', 'student.user'])
            ->get();
    }
}
