<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\AcademicTerm;
use App\Domain\Package\Models\Package;
use App\Domain\Package\Models\BranchPackage;
use App\Domain\Package\Services\PackageService;
use App\Domain\Onboarding\Services\OnboardingService;
use App\Domain\Onboarding\Models\InstitutionSetting;
use App\Domain\Onboarding\Models\OnboardingStep;
use App\Domain\Onboarding\Models\OnboardingChecklist;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\PackageSeeder;

class OnboardingWizardTest extends TestCase
{
    use RefreshDatabase;

    protected OnboardingService $onboardingService;
    protected PackageService $packageService;
    protected Branch $branch1;
    protected Branch $branch2;
    protected User $adminUser1;
    protected User $adminUser2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(FeatureSeeder::class);
        $this->seed(PackageSeeder::class);

        $this->onboardingService = app(OnboardingService::class);
        $this->packageService = app(PackageService::class);

        // Branch 1
        $this->branch1 = Branch::create([
            'name' => 'Test Dershanesi Kadıköy',
            'code' => 'BR-KDK',
            'slug' => 'br-kdk',
            'status' => 'active',
        ]);
        $this->adminUser1 = User::factory()->create([
            'branch_id' => $this->branch1->id,
        ]);
        $role = Role::firstOrCreate(['name' => 'Branch Admin']);
        $this->adminUser1->roles()->attach($role);
        $this->adminUser1->unsetRelation('roles');

        // Branch 2
        $this->branch2 = Branch::create([
            'name' => 'Test Dershanesi Beşiktaş',
            'code' => 'BR-BSK',
            'slug' => 'br-bsk',
            'status' => 'active',
        ]);
        $this->adminUser2 = User::factory()->create([
            'branch_id' => $this->branch2->id,
        ]);
        $this->adminUser2->roles()->attach($role);
        $this->adminUser2->unsetRelation('roles');
    }

    public function test_admin_can_access_onboarding_wizard_index(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->get(route('admin.onboarding.index'));

        // Should redirect to step 1 profile for new branch
        $response->assertRedirect(route('admin.onboarding.profile'));
    }

    public function test_institution_profile_can_be_saved(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.onboarding.saveProfile'), [
                'institution_name' => 'Kadıköy Fen Bilimleri Dershanesi',
                'phone' => '02163334455',
                'email' => 'kadikoy@fen.com',
                'address' => 'Bahariye Cad. No:45 Kadıköy',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
            ]);

        $response->assertRedirect(route('admin.onboarding.academic-year'));

        $this->assertDatabaseHas('institution_settings', [
            'branch_id' => $this->branch1->id,
            'institution_name' => 'Kadıköy Fen Bilimleri Dershanesi',
            'city' => 'İstanbul',
        ]);

        $this->assertDatabaseHas('onboarding_checklists', [
            'branch_id' => $this->branch1->id,
            'key' => 'institution_profile_completed',
            'completed' => true,
        ]);
    }

    public function test_academic_year_can_be_created_during_onboarding(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.onboarding.saveAcademicYear'), [
                'name' => '2026-2027 YKS Sezonu',
                'start_date' => '2026-09-01',
                'end_date' => '2027-06-30',
            ]);

        $response->assertRedirect(route('admin.onboarding.package'));

        $this->assertDatabaseHas('academic_terms', [
            'name' => '2026-2027 YKS Sezonu',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('onboarding_checklists', [
            'branch_id' => $this->branch1->id,
            'key' => 'academic_year_created',
            'completed' => true,
        ]);
    }

    public function test_package_can_be_selected_and_persisted_to_branch_packages(): void
    {
        $pkgV2 = Package::where('code', 'V2')->firstOrFail();

        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.onboarding.selectPackage'), [
                'package_id' => $pkgV2->id,
                'license_type' => 'three_year',
            ]);

        $response->assertRedirect(route('admin.onboarding.teacher'));

        $this->assertDatabaseHas('branch_packages', [
            'branch_id' => $this->branch1->id,
            'package_id' => $pkgV2->id,
            'license_type' => 'three_year',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('onboarding_checklists', [
            'branch_id' => $this->branch1->id,
            'key' => 'package_selected',
            'completed' => true,
        ]);
    }

    public function test_teacher_can_be_created_via_onboarding(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.onboarding.createTeacher'), [
                'first_name' => 'Mehmet',
                'last_name' => 'Kaya',
                'email' => 'mehmet.kaya@kadikoyfen.com',
                'phone' => '05331112233',
                'branch_subject' => 'Matematik',
            ]);

        $response->assertRedirect(route('admin.onboarding.classroom'));

        $this->assertDatabaseHas('users', [
            'branch_id' => $this->branch1->id,
            'email' => 'mehmet.kaya@kadikoyfen.com',
        ]);

        $this->assertDatabaseHas('teachers', [
            'branch_id' => $this->branch1->id,
            'specialties' => 'Matematik',
        ]);

        $this->assertDatabaseHas('onboarding_checklists', [
            'branch_id' => $this->branch1->id,
            'key' => 'teacher_added',
            'completed' => true,
        ]);
    }

    public function test_classroom_can_be_created_via_onboarding(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.onboarding.createClassroom'), [
                'name' => '12-Sayısal A',
                'code' => '12SAY-A',
                'capacity' => 20,
            ]);

        $response->assertRedirect(route('admin.onboarding.complete'));

        $this->assertDatabaseHas('classrooms', [
            'branch_id' => $this->branch1->id,
            'name' => '12-Sayısal A',
            'code' => '12SAY-A',
        ]);

        $this->assertDatabaseHas('onboarding_checklists', [
            'branch_id' => $this->branch1->id,
            'key' => 'classroom_created',
            'completed' => true,
        ]);
    }

    public function test_progress_calculation_is_accurate(): void
    {
        // Initially 0%
        $progress = $this->onboardingService->getProgress($this->branch1);
        $this->assertEquals(0, $progress['percentage']);
        $this->assertEquals(5, $progress['remaining_steps']);
        $this->assertFalse($progress['is_completed']);

        // Complete 3 steps
        $this->onboardingService->completeStep($this->branch1, 1, 'institution_profile_completed');
        $this->onboardingService->completeStep($this->branch1, 2, 'academic_year_created');
        $this->onboardingService->completeStep($this->branch1, 3, 'package_selected');

        $updatedProgress = $this->onboardingService->getProgress($this->branch1);
        $this->assertEquals(60, $updatedProgress['percentage']);
        $this->assertEquals(2, $updatedProgress['remaining_steps']);
        $this->assertFalse($updatedProgress['is_completed']);

        // Complete remaining 2 steps
        $this->onboardingService->completeStep($this->branch1, 4, 'teacher_added');
        $this->onboardingService->completeStep($this->branch1, 5, 'classroom_created');

        $finalProgress = $this->onboardingService->getProgress($this->branch1);
        $this->assertEquals(100, $finalProgress['percentage']);
        $this->assertEquals(0, $finalProgress['remaining_steps']);
        $this->assertTrue($finalProgress['is_completed']);
    }

    public function test_incomplete_onboarding_blocks_critical_management_routes(): void
    {
        $this->be($this->adminUser1);

        $middleware = new \App\Http\Middleware\EnsureOnboardingCompleted($this->onboardingService);

        $request = \Illuminate\Http\Request::create('/admin/exams', 'GET');
        $request->setUserResolver(fn() => $this->adminUser1);

        $response = $middleware->handle($request, function() {
            return response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(url('/setup-wizard'), $response->headers->get('Location'));
    }

    public function test_completed_onboarding_allows_access_to_critical_routes(): void
    {
        $this->be($this->adminUser1);

        // Mark all steps completed for Branch 1
        $this->onboardingService->completeStep($this->branch1, 1, 'institution_profile_completed');
        $this->onboardingService->completeStep($this->branch1, 2, 'academic_year_created');
        $this->onboardingService->completeStep($this->branch1, 3, 'package_selected');
        $this->onboardingService->completeStep($this->branch1, 4, 'teacher_added');
        $this->onboardingService->completeStep($this->branch1, 5, 'classroom_created');

        $middleware = new \App\Http\Middleware\EnsureOnboardingCompleted($this->onboardingService);

        $request = \Illuminate\Http\Request::create('/admin/exams', 'GET');
        $request->setUserResolver(fn() => $this->adminUser1);

        $response = $middleware->handle($request, function() {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_tenant_isolation_keeps_onboarding_data_separate_between_branches(): void
    {
        // Complete Step 1 for Branch 1
        $this->onboardingService->completeStep($this->branch1, 1, 'institution_profile_completed');

        $progress1 = $this->onboardingService->getProgress($this->branch1);
        $progress2 = $this->onboardingService->getProgress($this->branch2);

        $this->assertEquals(20, $progress1['percentage']);
        $this->assertEquals(0, $progress2['percentage']);

        $this->assertTrue($progress1['checklists']['institution_profile_completed']);
        $this->assertFalse($progress2['checklists']['institution_profile_completed']);
    }
}
