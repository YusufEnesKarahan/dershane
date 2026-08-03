<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Domain\Teacher\Services\TeacherPortalService;
use App\Domain\Attendance\Services\AttendanceManagementService;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\AttendanceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    public function __construct(
        protected TeacherPortalService $portalService,
        protected AttendanceManagementService $attendanceService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher && $user?->hasRole('Super Admin')) {
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
        $statuses = AttendanceStatus::query()->orderBy('name')->get();

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
            $students = Student::whereHas('classrooms', fn ($query) => $query->where('classrooms.id', $session->classroom_id))->get();
        }

        return view('teacher.attendance', compact('assignedClasses', 'sessions', 'session', 'students', 'statuses'));
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
        if (!$teacher && $user?->hasRole('Super Admin')) {
            $teacher = \App\Models\Teacher::first();
        }
        abort_unless($teacher, 403);

        try {
            // Service will validate teacher permissions and classroom assignments
            $this->attendanceService->takeAttendance($sessionId, $request->records, $teacher->id);
            return redirect()->back()->with('success', 'Yoklama kaydı başarıyla güncellendi.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }
}
