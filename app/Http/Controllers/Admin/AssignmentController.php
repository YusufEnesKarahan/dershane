<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Teacher;
use App\DTOs\Homework\CreateAssignmentDTO;
use App\Domain\Homework\Actions\CreateAssignmentAction;
use App\Core\Repositories\Interfaces\AssignmentRepositoryInterface;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct(protected AssignmentRepositoryInterface $repository) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Assignment::class);

        $assignments = $this->repository->paginate(15, $request->all());
        $classrooms = Classroom::all();
        $courses = Course::all();
        $teachers = Teacher::with('user')->get();

        return view('admin.assignments.index', compact('assignments', 'classrooms', 'courses', 'teachers'));
    }

    public function store(Request $request, CreateAssignmentAction $action)
    {
        $this->authorize('create', Assignment::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'teacher_id' => 'required|exists:teachers,id',
            'due_date' => 'required|date',
        ]);

        $dto = new CreateAssignmentDTO(
            $request->title,
            $request->code,
            (int) $request->teacher_id,
            $request->due_date,
            $request->description,
            $request->assignment_type ?? 'Classroom',
            $request->classroom_id ? (int) $request->classroom_id : null,
            $request->course_id ? (int) $request->course_id : null,
            (int) ($request->max_score ?? 100)
        );

        try {
            $assignment = $action->execute($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.assignments.submissions.index', $assignment->id)->with('success', 'Ödev tanımlandı. Teslimleri ve değerlendirmeleri yönetebilirsiniz.');
    }
}
