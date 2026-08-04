<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Attendance\Services\AttendanceReportService;

class StudentAttendanceController extends Controller
{
    public function __construct(
        protected AttendanceReportService $reportService
    ) {}

    public function index()
    {
        $student = auth()->user()?->student ?? \App\Models\Student::first();
        if (!$student) {
            abort(403, 'Öğrenci profili bulunamadı.');
        }

        $summary = $this->reportService->studentAttendanceSummary($student->branch_id, $student->id);
        
        $records = \App\Models\AttendanceRecord::with(['session.teacher', 'classroom'])
            ->where('student_id', $student->id)
            ->latest('attendance_date')
            ->paginate(15);
            
        return view('student.attendance.index', compact('summary', 'records'));
    }
}
