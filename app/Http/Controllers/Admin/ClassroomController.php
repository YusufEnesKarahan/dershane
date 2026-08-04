<?php

namespace App\Http\Controllers\Admin;

use App\Core\Context\TenantContext;
use App\Domain\Classroom\Services\ClassroomManagementService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassroomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
{
    protected ClassroomManagementService $classroomService;
    protected SubscriptionLimitService $limitService;

    public function __construct(
        ClassroomManagementService $classroomService,
        SubscriptionLimitService $limitService
    ) {
        $this->classroomService = $classroomService;
        $this->limitService = $limitService;
    }

    protected function getActiveBranchId(): int
    {
        return TenantContext::getActiveBranchId()
            ?? session('active_branch_id')
            ?? auth()->user()?->branch_id
            ?? \App\Models\Branch::value('id')
            ?? 1;
    }

    public function index()
    {
        $this->authorize('viewAny', Classroom::class);
        $branchId = $this->getActiveBranchId();

        $classrooms = Classroom::with(['teacher.user', 'type'])
            ->withCount('students')
            ->where('branch_id', $branchId)
            ->paginate(15);

        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $this->authorize('create', Classroom::class);
        $branchId = $this->getActiveBranchId();

        if (!$this->limitService->checkClassroomLimit($branchId)) {
            return redirect()->route('admin.classrooms.index')->with('error', 'Mevcut abonelik planınız sınıf limitine ulaştı.');
        }

        $teachers = Teacher::with('user')->where('branch_id', $branchId)->get();
        $types = ClassroomType::all(); // Alternatively, filter by branch if types are tenant-scoped

        return view('admin.classrooms.create', compact('teachers', 'types'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Classroom::class);
        $branchId = $this->getActiveBranchId();

        if (!$this->limitService->checkClassroomLimit($branchId)) {
            return redirect()->back()->with('error', 'Mevcut abonelik planınız sınıf limitine ulaştı.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('classrooms', 'name')->where('branch_id', $branchId)],
            'code' => 'nullable|string|max:50|unique:classrooms,code,NULL,id,branch_id,' . $branchId,
            'teacher_id' => 'nullable|exists:teachers,id',
            'classroom_type_id' => 'nullable|exists:classroom_types,id',
            'capacity' => 'required|integer|min:1',
            'color_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true; // default value for checkbox
        }

        try {
            $classroom = $this->classroomService->createClassroom($validated, $branchId);
            return redirect()->route('admin.classrooms.show', $classroom->id)
                             ->with('success', 'Sınıf başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sınıf oluşturulurken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Classroom $classroom)
    {
        $this->authorize('view', $classroom);
        
        $classroom->load(['teacher.user', 'type', 'students']);
        
        return view('admin.classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        $this->authorize('update', $classroom);
        $branchId = $this->getActiveBranchId();
        
        $teachers = Teacher::with('user')->where('branch_id', $branchId)->get();
        $types = ClassroomType::all();

        return view('admin.classrooms.edit', compact('classroom', 'teachers', 'types'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);
        $branchId = $this->getActiveBranchId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('classrooms', 'name')->where('branch_id', $branchId)->ignore($classroom->id)],
            'code' => 'nullable|string|max:50|unique:classrooms,code,' . $classroom->id . ',id,branch_id,' . $branchId,
            'teacher_id' => 'nullable|exists:teachers,id',
            'classroom_type_id' => 'nullable|exists:classroom_types,id',
            'capacity' => 'required|integer|min:1',
            'color_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        try {
            $this->classroomService->updateClassroom($classroom, $validated);
            return redirect()->route('admin.classrooms.show', $classroom->id)
                             ->with('success', 'Sınıf başarıyla güncellendi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sınıf güncellenirken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Classroom $classroom)
    {
        $this->authorize('delete', $classroom);

        try {
            $this->classroomService->deleteClassroom($classroom);
            return redirect()->route('admin.classrooms.index')
                             ->with('success', 'Sınıf başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sınıf silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Show view to manage students of a classroom
     */
    public function students(Classroom $classroom)
    {
        $this->authorize('update', $classroom);
        $branchId = TenantContext::getActiveBranchId();
        
        // Students currently in this classroom
        $enrolledStudents = $classroom->students;

        // Students in the branch but not in this classroom
        $availableStudents = Student::where('branch_id', $branchId)
            ->whereDoesntHave('classrooms', function ($query) use ($classroom) {
                $query->where('classrooms.id', $classroom->id);
            })
            ->get();

        return view('admin.classrooms.students', compact('classroom', 'enrolledStudents', 'availableStudents'));
    }

    /**
     * Attach (assign) students to classroom
     */
    public function attachStudents(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        try {
            $this->classroomService->attachStudents($classroom, $validated['student_ids']);
            return redirect()->back()->with('success', 'Öğrenciler başarıyla eklendi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'İşlem başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Detach (remove) students from classroom
     */
    public function detachStudents(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        try {
            $this->classroomService->detachStudents($classroom, $validated['student_ids']);
            return redirect()->back()->with('success', 'Öğrenciler başarıyla çıkarıldı.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'İşlem başarısız: ' . $e->getMessage());
        }
    }
}
