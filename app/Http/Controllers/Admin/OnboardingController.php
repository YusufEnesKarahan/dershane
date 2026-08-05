<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Onboarding\Services\OnboardingService;
use App\Domain\Package\Services\PackageService;
use App\Models\AcademicTerm;
use App\Models\Classroom;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;

class OnboardingController extends Controller
{
    public function __construct(
        protected OnboardingService $onboardingService,
        protected PackageService $packageService
    ) {}

    /**
     * Wizard main screen / step resolver.
     */
    public function index()
    {
        $progress = $this->onboardingService->getProgress();

        if ($progress['is_completed']) {
            return redirect()->route('admin.onboarding.complete');
        }

        $step = $progress['current_step'];

        return match ($step) {
            1 => redirect()->route('admin.onboarding.profile'),
            2 => redirect()->route('admin.onboarding.academic-year'),
            3 => redirect()->route('admin.onboarding.package'),
            4 => redirect()->route('admin.onboarding.teacher'),
            5 => redirect()->route('admin.onboarding.classroom'),
            default => redirect()->route('admin.onboarding.profile'),
        };
    }

    /**
     * STEP 1 — Kurum Bilgileri View.
     */
    public function profile()
    {
        $progress = $this->onboardingService->getProgress();
        $settings = $this->onboardingService->getInstitutionSettings();

        return view('admin.onboarding.profile', compact('progress', 'settings'));
    }

    /**
     * STEP 1 — Save Kurum Bilgileri.
     */
    public function saveProfile(Request $request)
    {
        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
        ]);

        $settings = $this->onboardingService->getInstitutionSettings();
        $settings->update($validated);

        $this->onboardingService->completeStep(null, 1, 'institution_profile_completed');

        return redirect()->route('admin.onboarding.academic-year')
            ->with('success', 'Kurum bilgileri başarıyla kaydedildi.');
    }

    /**
     * STEP 2 — Akademik Yıl View.
     */
    public function academicYear()
    {
        $progress = $this->onboardingService->getProgress();
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        $term = AcademicTerm::where('is_active', true)->first();

        return view('admin.onboarding.academic_year', compact('progress', 'term'));
    }

    /**
     * STEP 2 — Save Akademik Yıl.
     */
    public function saveAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $branchId = session('active_branch_id', auth()->user()->branch_id);

        AcademicTerm::query()->update(['is_active' => false]);

        AcademicTerm::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => true,
        ]);

        $this->onboardingService->completeStep(null, 2, 'academic_year_created');

        return redirect()->route('admin.onboarding.teacher')
            ->with('success', 'Akademik yıl dönemi başarıyla tanımlandı.');
    }

    /**
     * STEP 3 — Paket Seçimi View.
     */
    public function package()
    {
        $progress = $this->onboardingService->getProgress();
        $packages = $this->packageService->listPackages();
        $activePackage = $this->packageService->getActivePackage();

        return view('admin.onboarding.package', compact('progress', 'packages', 'activePackage'));
    }

    /**
     * STEP 3 — Select & Save Package.
     */
    public function selectPackage(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'license_type' => 'required|in:yearly,three_year',
        ]);

        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $this->packageService->changeBranchPackage(
            $branchId,
            $validated['package_id'],
            $validated['license_type']
        );

        $this->onboardingService->completeStep(null, 3, 'package_selected');

        return redirect()->route('admin.onboarding.teacher')
            ->with('success', 'Lisans paketi başarıyla seçildi ve tanımlandı.');
    }

    /**
     * STEP 4 — İlk Öğretmen Ekleme View.
     */
    public function teacher()
    {
        $progress = $this->onboardingService->getProgress();
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        $teachers = Teacher::where('branch_id', $branchId)->get();

        return view('admin.onboarding.teacher', compact('progress', 'teachers'));
    }

    /**
     * STEP 4 — Create Teacher.
     */
    public function createTeacher(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'branch_subject' => 'nullable|string|max:100',
        ]);

        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt('password123'),
            'branch_id' => $branchId,
            'status' => \App\Enums\UserStatus::ACTIVE,
        ]);

        $role = Role::firstOrCreate(['name' => 'Teacher']);
        $user->roles()->attach($role);

        Teacher::create([
            'branch_id' => $branchId,
            'user_id' => $user->id,
            'specialties' => $validated['branch_subject'] ?? 'Genel Öğretmen',
            'status' => 'active',
        ]);

        $this->onboardingService->completeStep(null, 4, 'teacher_added');

        return redirect()->route('admin.onboarding.classroom')
            ->with('success', 'İlk öğretmen başarıyla eklendi.');
    }

    /**
     * STEP 5 — İlk Sınıf Ekleme View.
     */
    public function classroom()
    {
        $progress = $this->onboardingService->getProgress();
        $branchId = session('active_branch_id', auth()->user()->branch_id);
        $classrooms = Classroom::where('branch_id', $branchId)->get();

        return view('admin.onboarding.classroom', compact('progress', 'classrooms'));
    }

    /**
     * STEP 5 — Create Classroom.
     */
    public function createClassroom(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
        ]);

        $branchId = session('active_branch_id', auth()->user()->branch_id);

        Classroom::create([
            'branch_id' => $branchId,
            'name' => $validated['name'],
            'code' => $validated['code'],
            'capacity' => $validated['capacity'],
        ]);

        $this->onboardingService->completeStep(null, 5, 'classroom_created');

        return redirect()->route('admin.onboarding.complete')
            ->with('success', 'İlk sınıf başarıyla oluşturuldu.');
    }

    /**
     * Onboarding Completion View.
     */
    public function complete()
    {
        $progress = $this->onboardingService->getProgress();
        return view('admin.onboarding.complete', compact('progress'));
    }
}
