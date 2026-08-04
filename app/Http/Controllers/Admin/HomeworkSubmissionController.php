<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Domain\Homework\Services\HomeworkSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeworkSubmissionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected HomeworkSubmissionService $submissionService) {}

    public function index(Homework $homework)
    {
        $this->authorize('viewAny', HomeworkSubmission::class);
        $submissions = $homework->submissions()->with('student.user')->get();
        return view('admin.homeworks.submissions.index', compact('homework', 'submissions'));
    }

    public function show(Homework $homework, HomeworkSubmission $submission)
    {
        $this->authorize('view', $submission);
        return view('admin.homeworks.submissions.show', compact('homework', 'submission'));
    }

    public function grade(Request $request, Homework $homework, HomeworkSubmission $submission)
    {
        $this->authorize('grade', $submission);

        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:' . $homework->max_score,
            'teacher_feedback' => 'nullable|string'
        ]);

        try {
            $this->submissionService->gradeSubmission($submission, $validated);
            return redirect()->back()->with('success', 'Ödev notlandırıldı.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
