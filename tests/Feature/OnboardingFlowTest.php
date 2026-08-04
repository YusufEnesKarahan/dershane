<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\AcademicTerm;
use App\Domain\Onboarding\Models\InstitutionSetting;
use App\Domain\Onboarding\Services\OnboardingService;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected OnboardingService $onboardingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupSaaSTenant();
        $this->onboardingService = app(OnboardingService::class);
    }

    public function test_redirects_to_onboarding_if_not_configured()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.onboarding.index'));

        $response->assertRedirect(route('admin.onboarding.profile'));
    }

    public function test_can_submit_identity_step()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.onboarding.profile'), [
            'institution_name' => 'Test Institution',
            'phone' => '02125554433',
            'email' => 'test@institution.com',
            'address' => 'Test Adres',
            'city' => 'İstanbul',
            'district' => 'Kayıt',
        ]);

        $this->assertDatabaseHas('institution_settings', [
            'institution_name' => 'Test Institution',
            'phone' => '02125554433',
        ]);

        $response->assertRedirect(route('admin.onboarding.academic-year'));
    }

    public function test_can_submit_term_step_and_advance_onboarding()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.onboarding.saveAcademicYear'), [
            'name' => 'Fall 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-01',
        ]);

        $this->assertDatabaseHas('academic_terms', [
            'name' => 'Fall 2026',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.onboarding.package'));
    }

    public function test_can_access_dashboard_after_onboarding()
    {
        $branchId = session('active_branch_id', $this->superAdmin->branch_id);
        $this->onboardingService->completeStep($branchId, 1, 'institution_profile_completed');
        $this->onboardingService->completeStep($branchId, 2, 'academic_year_created');
        $this->onboardingService->completeStep($branchId, 3, 'package_selected');
        $this->onboardingService->completeStep($branchId, 4, 'teacher_added');
        $this->onboardingService->completeStep($branchId, 5, 'classroom_created');

        $response = $this->actingAs($this->superAdmin)->get('/admin/reporting/dashboard');
        
        $response->assertStatus(200);
    }
}
