<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Domain\Attendance\Services\AttendanceManagementService;
use App\Domain\Attendance\Services\AttendanceReportService;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceManagementService $managementService,
        protected AttendanceReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceSession::class);
        $date = $request->get('date', today()->toDateString());
        $sessions = $this->reportService->getDailyAttendance(auth()->user()->branch_id, $date);
        
        return view('admin.attendance.index', compact('sessions', 'date'));
    }

    public function create()
    {
        $this->authorize('create', AttendanceSession::class);
        
        // Return view with classrooms and teachers to create a session
        $classrooms = \App\Models\Classroom::all();
        $teachers = \App\Models\Teacher::all();
        
        return view('admin.attendance.create', compact('classrooms', 'teachers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AttendanceSession::class);

        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:teachers,id',
            'session_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable'
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        
        $session = $this->managementService->createSession($validated);

        return redirect()->route('admin.attendance.show', $session)->with('success', 'Yoklama oturumu oluşturuldu.');
    }

    public function show(AttendanceSession $attendance) // Route uses {attendance}
    {
        $this->authorize('view', $attendance);
        
        $attendance->load('records.student', 'classroom', 'teacher');
        
        return view('admin.attendance.show', ['session' => $attendance]);
    }
    
    public function report(Request $request)
    {
        $this->authorize('viewAny', AttendanceSession::class); // using viewAny for report access
        
        $month = $request->get('month', today()->month);
        $year = $request->get('year', today()->year);
        
        $report = $this->reportService->monthlyAttendanceReport(auth()->user()->branch_id, $year, $month);
        
        return view('admin.attendance.report', compact('report', 'month', 'year'));
    }
}
