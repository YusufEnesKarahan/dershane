<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceStatus;
use App\DTOs\Attendance\BulkAttendanceDTO;
use App\Domain\Attendance\Actions\RecordBulkAttendanceAction;
use App\Domain\Attendance\Services\AttendanceSessionService;
use App\Domain\Attendance\Services\AttendanceAnalyticsService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceSessionService $sessionService,
        protected AttendanceAnalyticsService $analyticsService
    ) {}

    public function take(AttendanceSession $session)
    {
        $session->load(['classroom', 'course', 'teacher.user', 'attendances']);
        $students = $this->sessionService->getEligibleStudents($session);
        $statuses = AttendanceStatus::all();

        return view('admin.attendances.take', compact('session', 'students', 'statuses'));
    }

    public function storeBulk(Request $request, AttendanceSession $session, RecordBulkAttendanceAction $action)
    {
        $request->validate([
            'attendances' => 'required|array',
        ]);

        $dto = new BulkAttendanceDTO($session->id, $request->attendances);
        $action->execute($dto);

        return redirect()->route('admin.attendances.sessions.index')->with('success', 'Yoklama kayıtları başarıyla işlendi.');
    }

    public function analytics()
    {
        $summary = $this->analyticsService->getSummary();
        $riskStudents = $this->analyticsService->getRiskStudents(15.0);

        return view('admin.attendances.analytics', compact('summary', 'riskStudents'));
    }
}
