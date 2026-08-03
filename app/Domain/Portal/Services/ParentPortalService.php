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
     * Get attendance records for a specific child.
     */
    public function getChildAttendance(int $studentId, int $limit = 10): Collection
    {
        return Attendance::with(['session.course', 'session.classroom'])
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
        $attendances = Attendance::where('student_id', $studentId)->get();
        
        $total = $attendances->count();
        
        return [
            'total' => $total,
            'present' => $attendances->whereIn('attendance_status_id', ['P', 'Present', 'var', '1'])->count(),
            'absent' => $attendances->whereIn('attendance_status_id', ['A', 'Absent', 'yok', '2'])->count(),
            'late' => $attendances->whereIn('attendance_status_id', ['L', 'Late', 'gec', '3'])->count(),
            'excused' => $attendances->whereIn('attendance_status_id', ['E', 'Excused', 'izinli', '4'])->count(),
        ];
    }
}
