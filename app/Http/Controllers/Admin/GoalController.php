<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentGoal;
use App\Domain\Guidance\Services\GuidanceManagementService;

class GoalController extends Controller
{
    public function __construct(
        protected GuidanceManagementService $service
    ) {}

    public function index()
    {
        $this->authorize('viewAny', StudentGoal::class);
        $goals = StudentGoal::with('student')->paginate(15);
        return view('admin.guidance.goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', StudentGoal::class);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string',
            'target_value' => 'nullable|string',
            'deadline' => 'nullable|date'
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['status'] = 'Pending';

        $this->service->createGoal($validated);

        return redirect()->back()->with('success', 'Goal created successfully.');
    }

    public function update(Request $request, StudentGoal $goal)
    {
        $this->authorize('update', $goal);
        
        $validated = $request->validate([
            'current_value' => 'required|string',
            'status' => 'required|in:Pending,In Progress,Achieved,Failed',
        ]);

        $this->service->updateGoalProgress($goal, $validated['current_value'], $validated['status']);

        return redirect()->back()->with('success', 'Goal updated successfully.');
    }
}
