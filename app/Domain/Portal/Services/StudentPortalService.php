<?php

namespace App\Domain\Portal\Services;

use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Collection;

class StudentPortalService
{
    /**
     * Get the student profile for a user.
     */
    public function getStudentByUserId(int $userId): ?Student
    {
        return Student::with(['classrooms', 'enrollments.course'])->where('user_id', $userId)->first();
    }

    /**
     * Get the schedule for the student.
     */
    public function getSchedule(int $studentId): Collection
    {
        // Currently, class schedules are bound to classrooms or enrollments.
        // For this sprint, we can return the student's enrollments and classrooms.
        $student = Student::with(['classrooms.teacher', 'enrollments.course'])->findOrFail($studentId);
        
        $schedule = collect();
        foreach ($student->classrooms as $classroom) {
            $schedule->push([
                'type' => 'Classroom',
                'name' => $classroom->name,
                'teacher' => $classroom->teacher ? $classroom->teacher->user->name : 'N/A',
            ]);
        }
        
        foreach ($student->enrollments as $enrollment) {
            $schedule->push([
                'type' => 'Course',
                'name' => $enrollment->course->name,
                'status' => $enrollment->status,
            ]);
        }

        return $schedule;
    }

    /**
     * Get attendance records for the student.
     */
    public function getAttendance(int $studentId, int $limit = 10): Collection
    {
        return Attendance::with(['session.course', 'session.classroom'])
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get attendance stats for the student.
     */
    public function getAttendanceStats(int $studentId): array
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
