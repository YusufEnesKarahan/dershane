<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Domain\Teacher\Services\TeacherPortalService;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherHomeworkController extends Controller
{
    public function __construct(protected TeacherPortalService $portalService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher) {
            return redirect()->back();
        }

        $assignedClasses = $this->portalService->getAssignedClasses($teacher->id);
        
        $classrooms = Classroom::all();
        $courses = Course::all();

        // Get assignments created by teacher or for their classrooms
        $classroomIds = $assignedClasses->pluck('classroom_id')->toArray();
        $assignments = Assignment::whereIn('classroom_id', $classroomIds)->orderBy('due_date', 'desc')->get();

        $selectedAssignmentId = $request->query('assignment_id');
        $submissions = collect();
        $assignment = null;

        if ($selectedAssignmentId) {
            $assignment = Assignment::findOrFail($selectedAssignmentId);
            $submissions = AssignmentSubmission::with('student')->where('assignment_id', $selectedAssignmentId)->get();
        }

        return view('teacher.homework', compact('assignedClasses', 'assignments', 'assignment', 'submissions', 'classrooms', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'classroom_id' => 'required|exists:classrooms,id',
            'course_id' => 'required|exists:courses,id',
            'due_date' => 'required|date',
        ]);

        Assignment::create([
            'title' => $request->title,
            'content' => $request->content,
            'classroom_id' => (int) $request->classroom_id,
            'course_id' => (int) $request->course_id,
            'due_date' => $request->due_date,
            'status' => 'Published',
        ]);

        return redirect()->back()->with('success', 'Ödev başarıyla oluşturuldu ve yayınlandı.');
    }

    public function evaluate(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:assignment_submissions,id',
            'score' => 'required|numeric|min:0|max:100',
            'teacher_feedback' => 'nullable|string',
        ]);

        $submission = AssignmentSubmission::findOrFail($request->submission_id);
        $submission->update([
            'score' => (float) $request->score,
            'teacher_feedback' => $request->teacher_feedback,
            'status' => 'Graded',
        ]);

        return redirect()->back()->with('success', 'Ödev başarıyla değerlendirildi.');
    }
}
