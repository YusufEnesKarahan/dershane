<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Domain\Academic\Services\HomeworkSubmissionService;
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
        $this->authorize('submit', $homework);

        $validated = $request->validate([
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // max 10MB per file
        ]);

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('homework_submissions', 'public');
                $files[] = [
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        try {
            $studentId = auth()->user()->student->id;
            
            $existingSubmission = $homework->submissions()->where('student_id', $studentId)->first();
            
            if ($existingSubmission) {
                $this->submissionService->updateSubmission($existingSubmission, $files);
            } else {
                $this->submissionService->submitHomework($homework, $studentId, [], $files);
            }
            
            return redirect()->back()->with('success', 'Ödev başarıyla teslim edildi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
