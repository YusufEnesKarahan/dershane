<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentGuidanceRecord;
use App\Domain\Guidance\Services\GuidanceManagementService;

class GuidanceController extends Controller
{
    public function __construct(
        protected GuidanceManagementService $service
    ) {}

    public function index()
    {
        $this->authorize('viewAny', StudentGuidanceRecord::class);
        $records = StudentGuidanceRecord::with('student', 'teacher')->paginate(15);
        return view('admin.guidance.index', compact('records'));
    }

    public function create()
    {
        $this->authorize('create', StudentGuidanceRecord::class);
        return view('admin.guidance.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', StudentGuidanceRecord::class);
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_term_id' => 'required|exists:academic_terms,id',
            'category' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'meeting_date' => 'nullable|date',
            'next_follow_up' => 'nullable|date'
        ]);

        $validated['branch_id'] = auth()->user()->branch_id;
        $validated['status'] = 'Open';

        $this->service->createRecord($validated);

        return redirect()->route('admin.guidance.index')->with('success', 'Guidance record created successfully.');
    }

    public function show(StudentGuidanceRecord $guidance)
    {
        $this->authorize('view', $guidance);
        $guidance->load('student', 'teacher', 'academicTerm');
        return view('admin.guidance.show', compact('guidance'));
    }

    public function edit(StudentGuidanceRecord $guidance)
    {
        $this->authorize('update', $guidance);
        return view('admin.guidance.edit', compact('guidance'));
    }

    public function update(Request $request, StudentGuidanceRecord $guidance)
    {
        $this->authorize('update', $guidance);
        
        $validated = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'meeting_date' => 'nullable|date',
            'next_follow_up' => 'nullable|date'
        ]);

        $this->service->updateRecord($guidance, $validated);

        return redirect()->route('admin.guidance.index')->with('success', 'Guidance record updated successfully.');
    }

    public function destroy(StudentGuidanceRecord $guidance)
    {
        $this->authorize('delete', $guidance);
        $guidance->delete();
        return redirect()->route('admin.guidance.index')->with('success', 'Guidance record deleted.');
    }
}
