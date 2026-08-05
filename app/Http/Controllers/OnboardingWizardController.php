<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Onboarding\Services\OnboardingService;
use App\Models\PlatformAuditLog;
use App\Models\User;
use App\Models\OnboardingProgress;
use Illuminate\Support\Facades\Auth;

class OnboardingWizardController extends Controller
{
    public function __construct(
        protected OnboardingService $onboardingService
    ) {}

    public function welcome()
    {
        return view('onboarding.welcome');
    }

    public function company()
    {
        return view('onboarding.company');
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:institutions,name',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'city' => 'required|string|max:100',
        ], [
            'name.unique' => 'Bu dershane adı sistemde zaten kayıtlı.',
        ]);

        session(['onboarding.company' => $validated]);

        return redirect()->route('onboarding.admin');
    }

    public function admin()
    {
        return view('onboarding.admin');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        session(['onboarding.admin' => $validated]);

        return redirect()->route('onboarding.branch');
    }

    public function branch()
    {
        return view('onboarding.branch');
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255|unique:branches,name',
            'address' => 'required|string',
        ], [
            'branch_name.unique' => 'Bu şube adı sistemde zaten kayıtlı.',
        ]);

        session(['onboarding.branch' => $validated]);

        return redirect()->route('onboarding.plan');
    }

    public function plan()
    {
        return view('onboarding.plan');
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|string|in:starter,professional,enterprise',
        ]);

        session(['onboarding.plan' => $validated['plan']]);

        return redirect()->route('onboarding.completed');
    }

    public function completed()
    {
        return view('onboarding.completed');
    }

    public function complete(Request $request)
    {
        $companyData = session('onboarding.company');
        $adminData = session('onboarding.admin');
        $branchData = session('onboarding.branch');
        $planSlug = session('onboarding.plan', 'starter');

        if (!$companyData || !$adminData || !$branchData) {
            return redirect()->route('onboarding.welcome')->with('error', 'Eksik onboarding bilgisi. Lütfen baştan başlayın.');
        }

        // 1. Create Tenant (Institution)
        $tenant = $this->onboardingService->createTenant($companyData);
        PlatformAuditLog::record(null, 'tenant.created', $tenant);

        // 2. Create Default Branch
        $branch = $this->onboardingService->createDefaultBranch($tenant, $branchData);
        PlatformAuditLog::record(null, 'branch.created', $branch);

        // 3. Create Admin User (scoped to branch_id)
        $admin = $this->onboardingService->createAdminUser($tenant, $adminData);
        $admin->update(['branch_id' => $branch->id]);
        PlatformAuditLog::record($admin, 'admin.created', $admin);

        // 4. Assign Default Plan & License
        $subscription = $this->onboardingService->assignDefaultPlan($branch, $planSlug);
        PlatformAuditLog::record($admin, 'license.assigned', $subscription->license);

        // 5. Seed Optional Demo Data
        if ($request->filled('seed_demo')) {
            $this->onboardingService->seedDemoData($branch, $admin);
            PlatformAuditLog::record($admin, 'demo_data.seeded', $branch);
        }

        // Initialize onboarding progress checklist (New Wizard)
        $progress = OnboardingProgress::create([
            'branch_id' => $branch->id,
            'company_info_completed' => true,
            'first_branch_completed' => true,
            'teacher_added' => $request->filled('seed_demo') ? true : false,
            'student_added' => $request->filled('seed_demo') ? true : false,
            'course_created' => $request->filled('seed_demo') ? true : false,
            'exam_created' => $request->filled('seed_demo') ? true : false,
        ]);

        // Complete legacy onboarding checklist so that CheckOnboardingStatus / EnsureOnboardingCompleted middlewares pass
        \App\Domain\Onboarding\Models\OnboardingStep::updateOrCreate(
            ['branch_id' => $branch->id],
            ['step' => 5, 'status' => 'completed']
        );
        foreach (\App\Domain\Onboarding\Services\OnboardingService::CHECKLIST_KEYS as $key) {
            \App\Domain\Onboarding\Models\OnboardingChecklist::updateOrCreate(
                ['branch_id' => $branch->id, 'key' => $key],
                ['completed' => true]
            );
        }

        // Ensure default SystemIdentity exists
        if (!\App\Models\SystemIdentity::exists()) {
            \App\Models\SystemIdentity::create([
                'company_name' => $companyData['name'],
                'brand_name' => $companyData['name'],
            ]);
        }

        // Ensure default AcademicTerm exists
        if (!\App\Models\AcademicTerm::where('is_active', true)->exists()) {
            \App\Models\AcademicTerm::create([
                'name' => '2026-2027 Eğitim Öğretim Yılı',
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_active' => true,
            ]);
        }

        // Auto log in new user
        Auth::login($admin);

        // Set session active branch context
        session(['active_branch_id' => $branch->id]);

        // Clear wizard session
        session()->forget('onboarding');

        return redirect()->route('admin.dashboard')->with('success', 'Dershaneniz başarıyla oluşturuldu ve kuruldu.');
    }
}
