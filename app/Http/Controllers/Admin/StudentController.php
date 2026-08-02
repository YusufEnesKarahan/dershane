<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\DTOs\Student\CreateStudentDTO;
use App\DTOs\Student\UpdateStudentDTO;
use App\DTOs\Student\GuardianDTO;
use App\Domain\Student\Actions\CreateStudentAction;
use App\Domain\Student\Actions\TransferStudentAction;
use App\Domain\Student\Services\GuardianService;
use App\Domain\Student\Services\StudentAnalyticsService;
use App\Core\Repositories\Interfaces\StudentRepositoryInterface;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(
        protected StudentRepositoryInterface $repository,
        protected GuardianService $guardianService,
        protected StudentAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $students = $this->repository->paginate(15, $request->all());

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $this->authorize('create', Student::class);

        $branches = Branch::select('id', 'name')->get();
        $classrooms = Classroom::select('id', 'name')->get();

        return view('admin.students.edit', compact('branches', 'classrooms'));
    }

    public function store(Request $request, CreateStudentAction $action)
    {
        $this->authorize('create', Student::class);

        $request->validate([
            'student_number' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
        ]);

        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->branch_id != $request->branch_id) {
            abort(403, 'Unauthorized branch assignment.');
        }

        $dto = new CreateStudentDTO(
            $request->student_number,
            $request->first_name,
            $request->last_name,
            (int) $request->branch_id,
            $request->identity_number,
            $request->birth_date,
            $request->gender,
            $request->classroom_id ? (int) $request->classroom_id : null,
            $request->status ?? 'Active'
        );

        try {
            $student = $action->execute($dto);

            if ($request->guardian_name && $request->guardian_phone) {
                $this->guardianService->addGuardian(new GuardianDTO(
                    $student->id,
                    $request->guardian_name,
                    $request->guardian_relation ?? 'Vasi',
                    $request->guardian_phone,
                    $request->guardian_email
                ));
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.students.edit', $student->id)->with('success', 'Öğrenci kaydı başarıyla oluşturuldu.');
    }

    public function edit(Student $student)
    {
        $this->authorize('update', Student::class);

        $student = $this->repository->findById($student->id);
        $branches = Branch::select('id', 'name')->get();
        $classrooms = Classroom::select('id', 'name')->get();
        $courses = Course::select('id', 'name')->get();

        return view('admin.students.edit', compact('student', 'branches', 'classrooms', 'courses'));
    }

    public function update(Request $request, Student $student)
    {
        $this->authorize('update', Student::class);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'branch_id' => 'sometimes|exists:branches,id',
        ]);

        if ($request->has('branch_id') && !auth()->user()->hasRole('Super Admin') && auth()->user()->branch_id != $request->branch_id) {
            abort(403, 'Unauthorized branch assignment.');
        }

        $dto = UpdateStudentDTO::fromRequest($request->all());
        $this->repository->update($student, (array) $dto);

        return redirect()->route('admin.students.edit', $student->id)->with('success', 'Öğrenci profili güncellendi.');
    }

    public function updateStatus(Request $request, Student $student)
    {
        $this->authorize('update', Student::class);

        $request->validate([
            'status' => 'required|string|in:Active,Graduated,Dropped,Suspended',
        ]);

        $student->update(['status' => $request->status]);

        // If there's a StudentStatusHistory model, we could log it here:
        // \App\Models\StudentStatusHistory::create([...]);

        return redirect()->back()->with('success', 'Öğrenci durumu başarıyla güncellendi.');
    }

    public function transfer(Request $request, Student $student, TransferStudentAction $action)
    {
        $this->authorize('update', Student::class);

        $request->validate([
            'to_branch_id' => 'required|exists:branches,id',
        ]);

        if (!auth()->user()->hasRole('Super Admin') && auth()->user()->branch_id != $request->to_branch_id) {
            abort(403, 'Unauthorized branch transfer.');
        }

        $action->execute(
            $student,
            (int) $request->to_branch_id,
            $request->to_classroom_id ? (int) $request->to_classroom_id : null,
            $request->reason
        );

        return redirect()->back()->with('success', 'Öğrenci transferi başarıyla gerçekleştirildi.');
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', Student::class);

        $this->repository->delete($student);

        return redirect()->route('admin.students.index')->with('success', 'Öğrenci kaydı silindi.');
    }

    public function analytics()
    {
        $this->authorize('viewAny', Student::class);

        $summary = $this->analyticsService->getSummary();

        return view('admin.students.analytics', compact('summary'));
    }
}
