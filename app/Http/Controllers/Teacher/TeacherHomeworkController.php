<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Domain\Homework\Services\HomeworkManagementService;
use App\Domain\Homework\Services\HomeworkSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherHomeworkController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected HomeworkManagementService $homeworkService,
        protected HomeworkSubmissionService $submissionService
    ) {}

    public function index()
    {
        $teacherId = auth()->user()->teacher->id ?? \App\Models\Teacher::value('id') ?? 1;

        $homeworks = Homework::where('teacher_id', $teacherId)
            ->with(['course', 'classroom'])
            ->orderByDesc('created_at')
            ->get();
            
        return view('teacher.homeworks.index', compact('homeworks'));
    }

    public function show(Homework $homework)
    {
        $this->authorize('view', $homework);
        $submissions = $homework->submissions()->with('student.user')->get();
        return view('teacher.homeworks.show', compact('homework', 'submissions'));
    }

    public function grade(Request $request, Homework $homework, HomeworkSubmission $submission)
    {
        $this->authorize('grade', $submission);

        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:' . $homework->max_score,
            'teacher_feedback' => 'nullable|string'
        ]);

        try {
            $this->submissionService->gradeSubmission(
                $submission, 
                $validated
            );
            return redirect()->back()->with('success', 'Ödev notlandırıldı.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
