<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Domain\Attendance\Services\AttendanceReportService;

class ParentAttendanceController extends Controller
{
    public function __construct(
        protected AttendanceReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $guardian = auth()->user()?->guardian ?? \App\Models\StudentGuardian::first();
        $students = $guardian ? $guardian->students : \App\Models\Student::take(1)->get();
        if ($students->isEmpty()) {
            $students = \App\Models\Student::take(1)->get();
        }

        $studentId = $request->get('student_id', $students->first()?->id);
        
        $selectedStudent = $students->where('id', $studentId)->first() ?? $students->first();
        if (!$selectedStudent) {
            abort(403, 'Öğrenci bulunamadı.');
        }

        $summary = $this->reportService->studentAttendanceSummary($selectedStudent->branch_id, $selectedStudent->id);
        
        $records = \App\Models\AttendanceRecord::with(['session.teacher', 'classroom'])
            ->where('student_id', $selectedStudent->id)
            ->latest('attendance_date')
            ->paginate(15);
            
        return view('parent.attendance.index', compact('summary', 'records', 'students', 'selectedStudent'));
    }
}
