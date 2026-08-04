<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Guidance\Services\TeacherGuidanceService;
use App\Models\Student;

class TeacherGuidanceController extends Controller
{
    public function __construct(
        protected TeacherGuidanceService $service
    ) {}

    public function myStudents()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) abort(403);

        $students = Student::with('user', 'riskLevels')
            ->where('branch_id', auth()->user()->branch_id)
            ->get();

        return view('teacher.guidance.students', compact('students'));
    }

    public function myGuidance()
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) abort(403);

        $needsAttention = $this->service->getStudentsNeedingAttention($teacher->id, auth()->user()->branch_id);
        $upcomingMeetings = $this->service->getUpcomingMeetings($teacher->id, auth()->user()->branch_id);

        return view('teacher.guidance.dashboard', compact('needsAttention', 'upcomingMeetings'));
    }
}
