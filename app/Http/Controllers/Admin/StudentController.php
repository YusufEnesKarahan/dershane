<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Classroom;
use App\Domain\Student\Services\StudentManagementService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use App\Core\Context\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct(
        protected StudentManagementService $studentService,
        protected SubscriptionLimitService $limitService
    ) {}

    protected function getActiveBranchId(): int
    {
        return TenantContext::getActiveBranchId()
            ?? session('active_branch_id')
            ?? auth()->user()?->branch_id
            ?? Branch::value('id')
            ?? 1;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Student::class);

        $branchId = $this->getActiveBranchId();

        $filters = $request->only(['search', 'status']);
        $students = $this->studentService->getStudents($branchId, $filters);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $this->authorize('create', Student::class);

        $branchId = $this->getActiveBranchId();
        if (!$this->limitService->checkStudentLimit($branchId)) {
            return redirect()->route('admin.students.index')->with('error', 'Mevcut abonelik planınız öğrenci limitine ulaştı. Yeni öğrenci eklemek için paketinizi yükseltin.');
        }

        $classrooms = Classroom::select('id', 'name')->get();

        return view('admin.students.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Student::class);

        $branchId = $this->getActiveBranchId();
        
        if (!$this->limitService->checkStudentLimit($branchId)) {
            return redirect()->back()->with('error', 'Mevcut abonelik planınız öğrenci limitine ulaştı.');
        }

        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('students', 'student_number')->where('branch_id', $branchId)],
            'identity_number' => ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('students', 'identity_number')->whereNotNull('identity_number')],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'address_text' => 'nullable|string',
        ]);

        try {
            $student = $this->studentService->createStudent($validated, $branchId, Auth::id());
            return redirect()->route('admin.students.show', $student->id)->with('success', 'Öğrenci kaydı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Öğrenci oluşturulurken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Student $student)
    {
        $this->authorize('view', $student);

        $data = $this->studentService->getStudentDetail($student);
        
        return view('admin.students.show', $data);
    }

    public function edit(Student $student)
    {
        $this->authorize('update', $student);

        $classrooms = Classroom::select('id', 'name')->get();
        $student->load(['primaryGuardian', 'contact', 'address']);

        return view('admin.students.edit', compact('student', 'classrooms'));
    }

    public function update(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $branchId = $this->getActiveBranchId();

        $validated = $request->validate([
            'student_number' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::unique('students', 'student_number')->where('branch_id', $branchId)->ignore($student->id)],
            'identity_number' => ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('students', 'identity_number')->whereNotNull('identity_number')->ignore($student->id)],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'address_text' => 'nullable|string',
        ]);

        try {
            $this->studentService->updateStudent($student, $validated, Auth::id());
            return redirect()->route('admin.students.show', $student->id)->with('success', 'Öğrenci profili başarıyla güncellendi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        $this->studentService->deleteStudent($student, Auth::id());

        return redirect()->route('admin.students.index')->with('success', 'Öğrenci başarıyla silindi.');
    }
}
