<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\SystemIdentity;
use App\Models\AcademicTerm;
use App\Http\Middleware\CheckOnboardingStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CheckOnboardingStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_middleware_auto_heals_when_onboarding_completed_but_records_missing()
    {
        // 1. Create completed legacy onboarding checklist records
        $branch = Branch::create(['name' => 'Test Branch', 'slug' => 'test-branch']);
        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'email' => 'admin_test_onboarded@test.com',
        ]);

        \App\Domain\Onboarding\Models\OnboardingStep::create([
            'branch_id' => $branch->id,
            'step' => 5,
            'status' => 'completed',
        ]);

        foreach (\App\Domain\Onboarding\Services\OnboardingService::CHECKLIST_KEYS as $key) {
            \App\Domain\Onboarding\Models\OnboardingChecklist::updateOrCreate(
                ['branch_id' => $branch->id, 'key' => $key],
                ['completed' => true]
            );
        }

        // Initially SystemIdentity and AcademicTerm do NOT exist
        $this->assertFalse(SystemIdentity::exists());
        $this->assertFalse(AcademicTerm::exists());

        // Authenticate the user
        $this->actingAs($admin);

        // Set tenant context
        \App\Core\Context\TenantContext::setActiveBranchId($branch->id);

        // Create middleware request
        $request = Request::create('/admin/dashboard', 'GET');

        // Instantiate middleware
        $middleware = new CheckOnboardingStatus();

        // Run middleware
        $response = $middleware->handle($request, function ($req) {
            return response('Passed');
        });

        // Clear tenant context
        \App\Core\Context\TenantContext::clear();

        // The middleware should self-heal and allow the request to pass
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Passed', $response->getContent());

        // Assert SystemIdentity and AcademicTerm were auto-created
        $this->assertTrue(SystemIdentity::exists());
        $this->assertTrue(AcademicTerm::where('is_active', true)->exists());
    }

    public function test_middleware_redirects_to_onboarding_when_not_completed()
    {
        $branch = Branch::create(['name' => 'Unfinished Branch', 'slug' => 'unfinished-branch']);
        $admin = User::factory()->create([
            'branch_id' => $branch->id,
            'email' => 'admin_unfinished@test.com',
        ]);

        // Onboarding is NOT completed
        $this->assertFalse(SystemIdentity::exists());
        $this->assertFalse(AcademicTerm::exists());

        $this->actingAs($admin);

        \App\Core\Context\TenantContext::setActiveBranchId($branch->id);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new CheckOnboardingStatus();

        $response = $middleware->handle($request, function ($req) {
            return response('Passed');
        });

        \App\Core\Context\TenantContext::clear();

        // The response should be a redirect to onboarding index
        $this->assertTrue($response->isRedirect());
        $this->assertEquals(route('admin.onboarding.index'), $response->headers->get('Location'));
    }
}
