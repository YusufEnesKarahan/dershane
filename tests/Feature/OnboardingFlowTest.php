<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\SystemIdentity;
use App\Models\AcademicTerm;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupSaaSTenant();
    }

    public function test_redirects_to_onboarding_if_not_configured()
    {
        // Ensure not configured
        SystemIdentity::truncate();
        AcademicTerm::truncate();

        $response = $this->actingAs($this->superAdmin)->get('/admin/reporting/dashboard');

        // Should redirect to onboarding
        $response->assertRedirect(route('admin.onboarding.index'));
    }

    public function test_can_submit_identity_step()
    {
        SystemIdentity::truncate();

        $response = $this->actingAs($this->superAdmin)->post(route('admin.onboarding.identity'), [
            'company_name' => 'Test Company',
            'brand_name' => 'Test Brand',
        ]);

        $this->assertDatabaseHas('system_identity', [
            'company_name' => 'Test Company',
            'brand_name' => 'Test Brand',
        ]);

        $response->assertRedirect(route('admin.onboarding.index'));
    }

    public function test_can_submit_term_step_and_finish_onboarding()
    {
        SystemIdentity::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'company_name' => 'Test',
            'brand_name' => 'Test',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.onboarding.term'), [
            'name' => 'Fall 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-01',
        ]);

        $this->assertDatabaseHas('academic_terms', [
            'name' => 'Fall 2026',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.reporting.dashboard'));
    }

    public function test_can_access_dashboard_after_onboarding()
    {
        SystemIdentity::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'company_name' => 'Test',
            'brand_name' => 'Test',
        ]);

        AcademicTerm::create([
            'name' => 'Fall 2026',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-01',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->get('/admin/reporting/dashboard');
        
        $response->assertStatus(200);
    }
}
