<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\AttendanceStatus;
use App\Domain\Attendance\Services\AttendanceManagementService;
use App\Core\Context\TenantContext;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(protected AttendanceManagementService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceSession::class);
        $branchId = TenantContext::getActiveBranchId();

        $sessions = AttendanceSession::with(['classroom', 'course', 'teacher.user'])
            ->where('branch_id', $branchId)
            ->orderBy('session_date', 'desc')
            ->paginate(15);
            
        $classrooms = Classroom::where('branch_id', $branchId)->get();
        $courses = Course::where('branch_id', $branchId)->get();
        $teachers = Teacher::with('user')->where('branch_id', $branchId)->get();

        return view('admin.attendance.index', compact('sessions', 'classrooms', 'courses', 'teachers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AttendanceSession::class);
        $branchId = TenantContext::getActiveBranchId();

        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        try {
            $session = $this->service->createSession($validated, $branchId);
            return redirect()->route('admin.attendance.take', $session->id)->with('success', 'Oturum oluşturuldu, yoklama alabilirsiniz.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function take(AttendanceSession $attendance)
    {
        $session = $attendance;
        $this->authorize('update', $session);

        $session->load(['classroom', 'course', 'teacher.user', 'attendances.student']);
        
        $students = Student::query()
            ->where('branch_id', $session->branch_id)
            ->whereHas('classrooms', fn ($query) => $query->where('classrooms.id', $session->classroom_id))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
        $statuses = AttendanceStatus::query()->orderBy('name')->get();

        return view('admin.attendance.take', compact('session', 'students', 'statuses'));
    }

    public function storeBulk(Request $request, AttendanceSession $attendance)
    {
        $session = $attendance;
        $this->authorize('update', $session);

        $validated = $request->validate([
            'attendances' => 'required|array',
        ]);

        try {
            $this->service->takeAttendance($session->id, $validated['attendances']);
            return redirect()->route('admin.attendance.index')->with('success', 'Yoklama kaydedildi.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function show(AttendanceSession $attendance)
    {
        $session = $attendance;
        $this->authorize('view', $session);
        
        $session->load(['classroom', 'course', 'teacher.user', 'attendances.student', 'attendances.status']);

        return view('admin.attendance.show', compact('session'));
    }

    public function report(Request $request)
    {
        $this->authorize('report', AttendanceSession::class);
        $branchId = TenantContext::getActiveBranchId();

        $filters = $request->only(['start_date', 'end_date', 'classroom_id']);
        
        $reportData = $this->service->generateReports($branchId, $filters);
        $classrooms = Classroom::where('branch_id', $branchId)->get();

        return view('admin.attendance.report', [
            'summary' => $reportData['summary'],
            'records' => $reportData['records'],
            'classrooms' => $classrooms,
        ]);
    }
}
