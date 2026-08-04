<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Domain\Schedule\Services\ScheduleManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherScheduleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ScheduleManagementService $scheduleService
    ) {}

    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            abort(403, 'Öğretmen profili bulunamadı.');
        }

        $schedules = $this->scheduleService->getTeacherSchedules($teacher->id);

        return view('teacher.schedule.index', compact('schedules'));
    }
}
