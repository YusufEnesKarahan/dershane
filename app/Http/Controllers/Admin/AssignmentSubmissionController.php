<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Student;
use App\DTOs\Homework\SubmitAssignmentDTO;
use App\DTOs\Homework\EvaluateSubmissionDTO;
use App\Domain\Homework\Actions\SubmitAssignmentAction;
use App\Domain\Homework\Actions\EvaluateSubmissionAction;
use App\Domain\Homework\Services\AssignmentAnalyticsService;
use App\Core\Repositories\Interfaces\AssignmentSubmissionRepositoryInterface;
use Illuminate\Http\Request;

class AssignmentSubmissionController extends Controller
{
    public function __construct(
        protected AssignmentSubmissionRepositoryInterface $repository,
        protected AssignmentAnalyticsService $analyticsService
    ) {}

    public function index(Assignment $assignment)
    {
        $submissions = $this->repository->getByAssignment($assignment->id);
        $students = Student::all();

        return view('admin.assignments.submissions', compact('assignment', 'submissions', 'students'));
    }

    public function store(Request $request, Assignment $assignment, SubmitAssignmentAction $action)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $dto = new SubmitAssignmentDTO(
            $assignment->id,
            (int) $request->student_id,
            $request->remarks
        );

        try {
            $action->execute($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->back()->with('success', 'Ödev teslim kaydı eklendi.');
    }

    public function evaluate(Request $request, Assignment $assignment, EvaluateSubmissionAction $action)
    {
        $request->validate([
            'submission_id' => 'required|exists:assignment_submissions,id',
            'score' => 'required|numeric|min:0',
        ]);

        $dto = new EvaluateSubmissionDTO(
            (int) $request->submission_id,
            (float) $request->score,
            auth()->id(),
            $request->feedback
        );

        $action->execute($dto);

        return redirect()->back()->with('success', 'Ödev başarıyla puanlandı ve geri bildirim iletildi.');
    }

    public function analytics()
    {
        $summary = $this->analyticsService->getSummary();
        $assignments = Assignment::with(['classroom', 'teacher.user', 'submissions.score'])->orderBy('due_date', 'desc')->get();

        return view('admin.assignments.analytics', compact('summary', 'assignments'));
    }
}
