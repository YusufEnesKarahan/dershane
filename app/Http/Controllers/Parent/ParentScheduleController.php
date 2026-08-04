<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\LessonSchedule;
use App\Domain\Schedule\Services\ScheduleManagementService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ParentScheduleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ScheduleManagementService $scheduleService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $schedules = $this->scheduleService->getParentStudentSchedules($user);

        return view('parent.schedule.index', compact('schedules'));
    }
}
