<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Domain\Portal\Services\StudentPortalService;
use App\Domain\Exam\Services\StudentExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function __construct(
        protected StudentPortalService $service,
        protected StudentExamService $examService
    ) {}

    public function dashboard(Request $request)
    {
        $student = $this->service->getStudentByUserId(Auth::id());
        
        if (!$student) {
            abort(403, 'Öğrenci profili bulunamadı.');
        }

        $schedule = $this->service->getSchedule($student->id);
        $attendanceStats = $this->service->getAttendanceStats($student->id);
        $recentAttendance = $this->service->getAttendance($student->id, 5);
        $examResults = $this->examService->getStudentResults($student);

        return view('portal.student.dashboard', compact('student', 'schedule', 'attendanceStats', 'recentAttendance', 'examResults'));
    }
}
