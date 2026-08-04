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

    public function getSchedule(int $studentId)
    {
        $student = Student::find($studentId);
        if (!$student || !$student->classroom_id) {
            return collect();
        }

        return \App\Models\LessonSchedule::with(['course', 'teacher'])
            ->where('classroom_id', $student->classroom_id)
            ->get();
    }

    /**
     * Get the weekly schedule for the student.
     */
    public function getWeeklySchedule(int $studentId, int $academicTermId)
    {
        $student = Student::findOrFail($studentId);
        $classroomId = $student->classroom_id; // Assume main classroom for now
        
        if (!$classroomId) {
            return collect();
        }

        return app(\App\Domain\Academic\Services\LessonScheduleManagementService::class)
            ->getClassroomSchedule($student->branch_id, $classroomId, $academicTermId);
    }

    public function getAttendance(int $studentId, int $limit = 10): Collection
    {
        return \App\Models\AttendanceRecord::with(['session', 'classroom'])
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
        $records = \App\Models\AttendanceRecord::where('student_id', $studentId)->get();
        
        return [
            'total' => $records->count(),
            'present' => $records->whereIn('status', ['present', 'P', 'var'])->count(),
            'absent' => $records->whereIn('status', ['absent', 'A', 'yok'])->count(),
            'late' => $records->whereIn('status', ['late', 'L', 'gec'])->count(),
            'excused' => $records->whereIn('status', ['excused', 'E', 'izinli'])->count(),
        ];
    }

    public function getPendingHomeworks(int $studentId)
    {
        $student = Student::findOrFail($studentId);
        $classroomId = $student->classroom_id;

        return \App\Models\Homework::where('classroom_id', $classroomId)
            ->where('status', 'published')
            ->whereDoesntHave('submissions', function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->with(['course', 'teacher.user'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getUpcomingDeadlines(int $studentId, int $limit = 5)
    {
        $student = Student::findOrFail($studentId);
        $classroomId = $student->classroom_id;

        return \App\Models\Homework::where('classroom_id', $classroomId)
            ->where('status', 'published')
            ->where('due_date', '>', now())
            ->whereDoesntHave('submissions', function($q) use ($studentId) {
                $q->where('student_id', $studentId)
                  ->whereIn('status', ['submitted', 'late', 'graded']);
            })
            ->with('course')
            ->orderBy('due_date', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getFinancialSummary(int $studentId): array
    {
        $plans = \App\Models\PaymentPlan::where('student_id', $studentId)
            ->where('status', 'active')
            ->get();
            
        $totalDebt = $plans->sum('net_amount');
        
        $totalPaid = \App\Models\Payment::where('student_id', $studentId)
            ->whereHas('transactions', function($q) {
                $q->where('transaction_type', 'collection');
            })->sum('amount');
            
        $totalRefunded = \App\Models\Refund::whereHas('payment', function($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })->where('status', 'completed')->sum('amount');
        
        $netPaid = $totalPaid - $totalRefunded;
        
        $overdueAmount = \App\Models\Installment::whereHas('paymentPlan', function($q) use ($studentId) {
            $q->where('student_id', $studentId);
        })->where('status', 'overdue')->sum('remaining_amount');

        return [
            'total_debt' => $totalDebt,
            'net_paid' => $netPaid,
            'remaining_debt' => max(0, $totalDebt - $netPaid),
            'overdue_amount' => $overdueAmount
        ];
    }

    public function getUpcomingInstallments(int $studentId, int $limit = 5)
    {
        return \App\Models\Installment::whereHas('paymentPlan', function($q) use ($studentId) {
            $q->where('student_id', $studentId)->where('status', 'active');
        })
        ->whereIn('status', ['pending', 'partial', 'overdue'])
        ->orderBy('due_date', 'asc')
        ->limit($limit)
        ->get();
    }
}
