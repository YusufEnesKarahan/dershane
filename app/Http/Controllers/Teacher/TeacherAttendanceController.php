<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Domain\Attendance\Services\AttendanceManagementService;
use App\Domain\Attendance\Services\AttendanceReportService;

class TeacherAttendanceController extends Controller
{
    public function __construct(
        protected AttendanceManagementService $managementService,
        protected AttendanceReportService $reportService
    ) {}

    public function myClasses(Request $request)
    {
        $teacherId = auth()->user()->teacher->id;
        $date = $request->get('date', today()->toDateString());
        
        $sessions = AttendanceSession::where('teacher_id', $teacherId)
            ->where('session_date', $date)
            ->get();
            
        return view('teacher.attendance.index', compact('sessions', 'date'));
    }

    public function takeAttendance(AttendanceSession $session)
    {
        $this->authorize('update', $session);
        
        $session->load('classroom.students', 'records');
        
        return view('teacher.attendance.create', compact('session'));
    }

    public function updateAttendance(Request $request, AttendanceSession $session)
    {
        $this->authorize('update', $session);
        
        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status' => 'required|in:present,absent,late,excused',
            'records.*.note' => 'nullable|string'
        ]);
        
        $this->managementService->bulkMarkAttendance($session, $validated['records'], auth()->id());
        
        return redirect()->route('teacher.attendance.index')->with('success', 'Yoklama başarıyla kaydedildi.');
    }
}
