<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Domain\Schedule\Services\ScheduleManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentScheduleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ScheduleManagementService $scheduleService
    ) {}

    public function index(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403, 'Öğrenci profili bulunamadı.');
        }

        $schedules = $this->scheduleService->getStudentSchedules($student);

        return view('student.schedule.index', compact('schedules'));
    }
}
