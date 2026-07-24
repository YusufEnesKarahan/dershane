<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Teacher;
use App\DTOs\Attendance\CreateAttendanceSessionDTO;
use App\DTOs\Attendance\BulkAttendanceDTO;
use App\Domain\Attendance\Actions\CreateAttendanceSessionAction;
use App\Domain\Attendance\Actions\RecordBulkAttendanceAction;
use App\Domain\Attendance\Services\AttendanceAnalyticsService;
use App\Core\Repositories\Interfaces\AttendanceSessionRepositoryInterface;
use App\Models\AttendanceStatus;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceSessionController extends Controller
{
    public function __construct(protected AttendanceSessionRepositoryInterface $repository) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceSession::class);

        $sessions = $this->repository->paginate(15, $request->all());
        $classrooms = Classroom::all();
        $courses = Course::all();
        $teachers = Teacher::with('user')->get();

        return view('admin.attendances.index', compact('sessions', 'classrooms', 'courses', 'teachers'));
    }

    public function store(Request $request, CreateAttendanceSessionAction $action)
    {
        $this->authorize('create', AttendanceSession::class);

        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $dto = new CreateAttendanceSessionDTO(
            (int) $request->classroom_id,
            (int) $request->course_id,
            (int) $request->teacher_id,
            $request->session_date,
            $request->start_time,
            $request->end_time
        );

        $session = $action->execute($dto);

        return redirect()->route('admin.attendances.sessions.take', $session->id)->with('success', 'Ders oturumu oluşturuldu. Şimdi yoklamayı alabilirsiniz.');
    }

    public function take(AttendanceSession $session)
    {
        $this->authorize('view', $session);

        $session->load(['classroom', 'course', 'teacher.user', 'attendances']);
        $students = Student::query()
            ->where('classroom_id', $session->classroom_id)
            ->whereHas('courses', fn ($query) => $query->whereKey($session->course_id))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $statuses = AttendanceStatus::query()->orderBy('name')->get();

        return view('admin.attendances.take', compact('session', 'students', 'statuses'));
    }

    public function storeBulk(Request $request, AttendanceSession $session, RecordBulkAttendanceAction $action)
    {
        $this->authorize('update', $session);

        $validated = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'integer', 'exists:students,id'],
            'attendances.*.attendance_status_id' => ['required', 'integer', 'exists:attendance_statuses,id'],
            'attendances.*.remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $action->execute(new BulkAttendanceDTO($session->id, $validated['attendances']));

        return redirect()->route('admin.attendances.sessions.index')->with('success', 'Yoklama kayıtları güncellendi.');
    }

    public function analytics(AttendanceAnalyticsService $analyticsService)
    {
        $this->authorize('viewAny', AttendanceSession::class);

        return view('admin.attendances.analytics', [
            'summary' => $analyticsService->getSummary(),
            'riskStudents' => $analyticsService->getRiskStudents(),
        ]);
    }
}
