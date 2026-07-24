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
        if (!$teacher && $user?->hasRole('Administrator')) {
            $teacher = \App\Models\Teacher::first();
        }
        if (!$teacher) {
            return redirect()->back();
        }

        $assignedClasses = $this->portalService->getAssignedClasses($teacher->id);
        $sessions = AttendanceSession::query()
            ->with('classroom')
            ->where('teacher_id', $teacher->id)
            ->orderBy('session_date', 'desc')
            ->get();

        $selectedSessionId = $request->query('session_id');
        $students = collect();
        $session = null;

        if ($selectedSessionId) {
            $session = AttendanceSession::find($selectedSessionId);
            if ($session && $session->teacher_id !== $teacher->id) {
                abort(403, 'Bu yoklama oturumuna erişim yetkiniz yok.');
            }
            if (!$session) {
                abort(404);
            }
            abort_unless(
                $this->portalService->canManageClassCourse($teacher->id, $session->classroom_id, $session->course_id),
                403
            );
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
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher && $user?->hasRole('Administrator')) {
            $teacher = \App\Models\Teacher::first();
        }
        abort_unless($teacher, 403);

        $session = AttendanceSession::find($sessionId);
        if ($session && $session->teacher_id !== $teacher->id) {
            abort(403, 'Bu yoklama oturumuna erişim yetkiniz yok.');
        }
        if (!$session) {
            abort(404);
        }
        abort_unless(
            $this->portalService->canManageClassCourse($teacher->id, $session->classroom_id, $session->course_id),
            403
        );

        $studentIds = array_map('intval', array_keys($request->records));
        $allowedStudentCount = Student::query()
            ->where('classroom_id', $session->classroom_id)
            ->whereIn('id', $studentIds)
            ->count();
        abort_unless($allowedStudentCount === count(array_unique($studentIds)), 403);

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
