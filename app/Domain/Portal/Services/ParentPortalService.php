<?php

namespace App\Domain\Portal\Services;

use App\Models\StudentGuardian;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Collection;
use App\Core\Context\TenantContext;

class ParentPortalService
{
    /**
     * Get the guardian profile for a user.
     */
    public function getGuardianByUserId(int $userId): ?StudentGuardian
    {
        return StudentGuardian::where('user_id', $userId)->first();
    }

    /**
     * Get children belonging to the guardian.
     */
    public function getChildren(int $guardianId): Collection
    {
        $guardian = StudentGuardian::findOrFail($guardianId);
        
        // Find all students related to this guardian
        return Student::whereHas('guardians', function ($query) use ($guardian) {
            $query->where('guardian_name', $guardian->guardian_name)
                  ->where('phone', $guardian->phone);
        })
        ->orWhere('id', $guardian->student_id)
        ->with(['classrooms', 'enrollments.course'])
        ->get();
    }

    /**
     * Verify if a parent has access to a specific student.
     */
    public function canAccessStudent(int $guardianId, int $studentId): bool
    {
        $children = $this->getChildren($guardianId);
        return $children->contains('id', $studentId);
    }

    /**
     * Get weekly schedule for a specific child.
     */
    public function getStudentSchedule(int $studentId, int $academicTermId)
    {
        $student = Student::findOrFail($studentId);
        $classroomId = $student->classroom_id;
        
        if (!$classroomId) {
            return collect();
        }

        return app(\App\Domain\Academic\Services\LessonScheduleManagementService::class)
            ->getClassroomSchedule($student->branch_id, $classroomId, $academicTermId);
    }

    public function getChildAttendance(int $studentId, int $limit = 10): Collection
    {
        return \App\Models\AttendanceRecord::with(['session', 'classroom'])
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overall attendance stats for a specific child.
     */
    public function getChildAttendanceStats(int $studentId): array
    {
        $records = \App\Models\AttendanceRecord::where('student_id', $studentId)->get();
        
        return [
            'total' => $records->count(),
            'present' => $records->whereIn('status', ['present', 'P', 'var'])->count(),
            'absent' => $records->whereIn('status', ['absent', 'A', 'yok'])->count(),
            'late' => $records->whereIn('status', ['late', 'L', 'gec'])->count(),
            'excused' => $records->whereIn('status', ['excused', 'E', 'izinli'])->count(),
        ];
    }

    public function getChildHomeworkStatus(int $studentId)
    {
        $student = Student::findOrFail($studentId);
        $classroomId = $student->classroom_id;

        $homeworks = \App\Models\Homework::where('classroom_id', $classroomId)
            ->where('status', 'published')
            ->with(['course', 'submissions' => function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            }])
            ->orderBy('due_date', 'desc')
            ->get();

        return $homeworks;
    }

    public function getChildFinancialSummary(int $studentId): array
    {
        return app(StudentPortalService::class)->getFinancialSummary($studentId);
    }

    public function getChildUpcomingInstallments(int $studentId, int $limit = 5)
    {
        return app(StudentPortalService::class)->getUpcomingInstallments($studentId, $limit);
    }
}
