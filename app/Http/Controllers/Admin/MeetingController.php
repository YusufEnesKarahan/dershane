<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentMeeting;
use App\Domain\Guidance\Services\GuidanceManagementService;

class MeetingController extends Controller
{
    public function __construct(
        protected GuidanceManagementService $service
    ) {}

    public function index()
    {
        $this->authorize('viewAny', StudentMeeting::class);
        $meetings = StudentMeeting::with('student', 'teacher')->paginate(15);
        return view('admin.guidance.meetings.index', compact('meetings'));
    }

    public function create()
    {
        $this->authorize('create', StudentMeeting::class);
        return view('admin.guidance.meetings.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', StudentMeeting::class);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:teachers,id',
            'meeting_date' => 'required|date',
            'meeting_type' => 'required|string',
            'summary' => 'nullable|string',
            'action_plan' => 'nullable|string',
            'next_meeting' => 'nullable|date'
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;

        $this->service->scheduleMeeting($validated);

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting scheduled successfully.');
    }

    public function show(StudentMeeting $meeting)
    {
        $this->authorize('view', $meeting);
        $meeting->load('student', 'teacher');
        return view('admin.guidance.meetings.show', compact('meeting'));
    }
}
