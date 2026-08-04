<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Guidance\Services\ParentGuidanceService;
use App\Models\StudentGuardian;

class ParentGuidanceController extends Controller
{
    public function __construct(
        protected ParentGuidanceService $service
    ) {}

    public function childPerformance(Request $request)
    {
        $guardian = auth()->user()->guardian;
        if (!$guardian) abort(403);

        // Assume we get the first child or from request
        $studentId = $request->get('student_id', $guardian->students()->first()?->id);
        if (!$studentId) return redirect()->back()->with('error', 'No child found.');

        $performance = $this->service->getChildPerformance($studentId, auth()->user()->branch_id);

        return view('parent.guidance.performance', compact('performance', 'studentId'));
    }

    public function childGuidance()
    {
        $guardian = auth()->user()->guardian;
        if (!$guardian) abort(403);

        $upcomingMeetings = $this->service->getUpcomingMeetings($guardian->id, auth()->user()->branch_id);

        return view('parent.guidance.dashboard', compact('upcomingMeetings'));
    }
}
