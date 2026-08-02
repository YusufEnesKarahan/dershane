<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Plan;
use App\Models\License;
use App\Models\Student;
use App\Domain\Platform\Services\LicenseLimitService;

class SaaSLicenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Base seed
        $this->artisan('db:seed', ['--class' => 'PlanSeeder']);
        $this->setupSaaSTenant();
    }

    public function test_can_create_plan()
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 99.00,
            'billing_period' => 'monthly',
            'is_active' => true,
            'limits' => [
                'students' => 10,
                'users' => 2,
                'branches' => 1,
            ]
        ]);

        $this->assertDatabaseHas('plans', [
            'slug' => 'test-plan',
            'price' => 99.00
        ]);
        
        $this->assertEquals(10, $plan->limits['students']);
    }

    public function test_trial_license_creation_and_expiration()
    {
        $plan = Plan::where('slug', 'starter')->first();

        $license = License::create([
            'license_key' => 'TRIAL-1234',
            'status' => 'trial',
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'trial_ends_at' => now()->subDay(), // expired
        ]);

        $this->assertTrue($license->isExpired());
        $this->assertFalse($license->isActive());

        $license2 = License::create([
            'license_key' => 'TRIAL-5678',
            'status' => 'trial',
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(14), // active
        ]);

        $this->assertFalse($license2->isExpired());
        $this->assertTrue($license2->isActive());
    }

    public function test_student_limit_check()
    {
        $plan = Plan::create([
            'name' => 'Limit Plan',
            'slug' => 'limit-plan',
            'limits' => [
                'students' => 2,
            ]
        ]);

        License::query()->delete();

        License::create([
            'license_key' => 'LIMIT-1234',
            'status' => 'active',
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        $limitService = new LicenseLimitService();
        
        $this->assertTrue($limitService->canCreateStudent());

        // We already have some students from setupSaaSTenant (1 student usually? wait, dummy might not have one)
        // Let's count existing
        $currentCount = Student::withoutGlobalScopes()->count();
        
        // Add students up to limit
        for ($i = $currentCount; $i < 2; $i++) {
            \Illuminate\Support\Facades\DB::table('students')->insert([
                'first_name' => 'Test',
                'last_name' => 'User',
                'student_number' => 'TST-' . $i,
                'branch_id' => $this->branch->id,
                'gender' => 'Male',
                'status' => 'Active',
            ]);
        }
        
        $this->assertFalse($limitService->canCreateStudent());
    }
}
