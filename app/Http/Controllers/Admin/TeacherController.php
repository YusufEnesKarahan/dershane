<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Domain\Teacher\Services\TeacherManagementService;
use App\Domain\Teacher\Services\TeacherAnalyticsService;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use App\Core\Context\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function __construct(
        protected TeacherManagementService $teacherService,
        protected TeacherAnalyticsService $analyticsService,
        protected SubscriptionLimitService $limitService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Teacher::class);
        $branchId = TenantContext::getActiveBranchId();

        $filters = $request->only(['search', 'status']);
        $teachers = $this->teacherService->getTeachers($branchId, $filters);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $this->authorize('create', Teacher::class);
        $branchId = TenantContext::getActiveBranchId();
        
        if (!$this->limitService->checkTeacherLimit($branchId)) {
            return redirect()->back()->with('error', 'Mevcut abonelik planınız öğretmen limitine ulaştı.');
        }

        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Teacher::class);
        $branchId = TenantContext::getActiveBranchId();
        
        if (!$this->limitService->checkTeacherLimit($branchId)) {
            return redirect()->back()->with('error', 'Mevcut abonelik planınız öğretmen limitine ulaştı.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'title' => 'nullable|string|max:255',
            'specialties' => 'nullable|string',
            'education' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        try {
            $teacher = $this->teacherService->createTeacher($validated, $branchId, Auth::id());
            return redirect()->route('admin.teachers.show', $teacher->id)->with('success', 'Öğretmen kaydı başarıyla oluşturuldu.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Öğretmen oluşturulurken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Teacher $teacher)
    {
        $this->authorize('view', $teacher);
        $data = $this->teacherService->getTeacherDetail($teacher);

        return view('admin.teachers.show', $data);
    }

    public function edit(Teacher $teacher)
    {
        $this->authorize('update', $teacher);
        $teacher->load('user');
        
        // Split name for edit form
        $nameParts = explode(' ', $teacher->user->name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        return view('admin.teachers.edit', compact('teacher', 'firstName', 'lastName'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'title' => 'nullable|string|max:255',
            'specialties' => 'nullable|string',
            'education' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        try {
            $this->teacherService->updateTeacher($teacher, $validated, Auth::id());
            return redirect()->route('admin.teachers.show', $teacher->id)->with('success', 'Öğretmen profili başarıyla güncellendi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Öğretmen güncellenirken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);

        try {
            $this->teacherService->deleteTeacher($teacher, Auth::id());
            return redirect()->route('admin.teachers.index')->with('success', 'Öğretmen başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Öğretmen silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function analytics(Teacher $teacher)
    {
        $this->authorize('view', $teacher);
        $analytics = $this->analyticsService->getAnalyticsSummary($teacher->id);
        return view('admin.teachers.analytics', compact('analytics'));
    }
}
