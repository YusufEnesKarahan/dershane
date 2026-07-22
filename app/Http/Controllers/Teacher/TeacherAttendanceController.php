<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Domain\Teacher\Services\TeacherPortalService;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    public function __construct(protected TeacherPortalService $portalService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher) {
            return redirect()->back();
        }

        $assignedClasses = $this->portalService->getAssignedClasses($teacher->id);
        $sessions = AttendanceSession::with('classroom')->orderBy('session_date', 'desc')->get();

        $selectedSessionId = $request->query('session_id');
        $students = collect();
        $session = null;

        if ($selectedSessionId) {
            $session = AttendanceSession::findOrFail($selectedSessionId);
            $students = Student::where('classroom_id', $session->classroom_id)->get();
        }

        return view('teacher.attendance', compact('assignedClasses', 'sessions', 'session', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id',
            'records' => 'required|array',
        ]);

        $sessionId = (int) $request->session_id;

        foreach ($request->records as $studentId => $statusCode) {
            $statusRecord = AttendanceStatus::where('code', strtoupper($statusCode))->first();
            $statusId = $statusRecord ? $statusRecord->id : 1;

            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $sessionId,
                    'student_id' => (int) $studentId,
                ],
                [
                    'attendance_status_id' => $statusId,
                ]
            );
        }

        return redirect()->back()->with('success', 'Yoklama kaydı başarıyla güncellendi.');
    }
}
