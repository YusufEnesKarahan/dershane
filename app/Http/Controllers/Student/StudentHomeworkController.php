<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Domain\Homework\Services\HomeworkSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentHomeworkController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected HomeworkSubmissionService $submissionService) {}

    public function index()
    {
        $studentId = auth()->user()->student->id ?? null;
        if (!$studentId) abort(403);

        $homeworks = \App\Models\Homework::where('classroom_id', auth()->user()->student->classroom_id)
            ->where('status', 'published')
            ->with(['course', 'teacher.user', 'submissions' => function($q) use ($studentId) {
                $q->where('student_id', $studentId);
            }])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('student.homeworks.index', compact('homeworks'));
    }

    public function show(Homework $homework)
    {
        // View policy covers whether this homework belongs to student's classroom
        $this->authorize('view', $homework);
        
        $studentId = auth()->user()->student->id;
        $submission = $homework->submissions()->where('student_id', $studentId)->first();

        return view('student.homeworks.show', compact('homework', 'submission'));
    }

    public function submit(Request $request, Homework $homework)
    {
        // TODO: authorize

        $validated = $request->validate([
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('file')) {
            $attachmentPath = $request->file('file')->store('homework_submissions', 'public');
        }

        try {
            $studentId = auth()->user()->student->id;
            
            $existingSubmission = $homework->submissions()->where('student_id', $studentId)->first();
            
            if ($existingSubmission) {
                // If there's an existing submission and we want to allow updating...
                // The new service submitHomework will actually update it due to updateOrCreate
                $this->submissionService->submitHomework($homework, $studentId, [], $attachmentPath);
            } else {
                $this->submissionService->submitHomework($homework, $studentId, [], $attachmentPath);
            }
            
            return redirect()->back()->with('success', 'Ödev başarıyla teslim edildi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
