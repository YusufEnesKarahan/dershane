<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\License;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Role;
use App\Domain\License\Services\LicenseService;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Core\Context\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $branch1;
    protected $branch2;
    protected $planStarter;
    protected $license1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOnboardingCompleted::class,
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $rSuperAdmin = Role::where('name', 'Super Admin')->first();

        // Create branches
        $this->branch1 = Branch::create([
            'name' => 'Adana Şubesi',
            'slug' => 'adana-subesi',
            'phone' => '5551119988',
            'email' => 'adana-lic@test.com',
            'address' => 'Adana'
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Mersin Şubesi',
            'slug' => 'mersin-subesi',
            'phone' => '5552229988',
            'email' => 'mersin-lic@test.com',
            'address' => 'Mersin'
        ]);

        // Create Plan
        $this->planStarter = Plan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'description' => 'Başlangıç paketi',
            'price' => 1000,
            'max_students' => 200,
            'max_teachers' => 10,
            'max_classrooms' => 5,
            'is_active' => true,
            'features' => ['student_portal' => true, 'advanced_reports' => false],
            'limits' => ['students' => 200, 'teachers' => 10]
        ]);

        // Create Admin User
        $this->adminUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $this->adminUser->roles()->sync([$rSuperAdmin->id]);

        // Create License & Subscription
        $this->license1 = License::create([
            'license_key' => 'LIC-ADANA-2026',
            'status' => 'active',
            'plan_id' => $this->planStarter->id,
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        Subscription::create([
            'license_id' => $this->license1->id,
            'branch_id' => $this->branch1->id,
            'plan_id' => $this->planStarter->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'expires_at' => now()->addYear(),
        ]);
    }

    public function test_active_license_allows_access()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->branch1->id]);

        $subService = app(SubscriptionService::class);
        $this->assertFalse($subService->isExpired($this->branch1->id));

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_expired_license_blocks_access()
    {
        $this->license1->update([
            'status' => 'expired',
            'expires_at' => now()->subDay()
        ]);

        $this->license1->subscription->update([
            'status' => 'expired',
            'expires_at' => now()->subDay(),
            'ends_at' => now()->subDay()
        ]);

        $subService = app(SubscriptionService::class);
        $this->assertTrue($subService->isExpired($this->branch1->id));
    }

    public function test_suspended_license_blocks_access()
    {
        $licService = app(LicenseService::class);
        $licService->suspendLicense($this->license1);

        $this->assertEquals('suspended', $this->license1->fresh()->status);
        $this->assertEquals('suspended', $this->license1->subscription->fresh()->status);
    }

    public function test_plan_limit_prevents_student_creation()
    {
        $subService = app(SubscriptionService::class);

        $hasLimit = $subService->checkLimit($this->branch1->id, 'students');
        $this->assertTrue($hasLimit);
    }

    public function test_feature_flag_controls_module_access()
    {
        $subService = app(SubscriptionService::class);

        $hasStudentPortal = $subService->hasFeature($this->branch1->id, 'student_portal');
        $this->assertTrue($hasStudentPortal);

        $hasAdvancedReports = $subService->hasFeature($this->branch1->id, 'advanced_reports');
        $this->assertFalse($hasAdvancedReports);
    }

    public function test_license_renewal_extends_date()
    {
        $licService = app(LicenseService::class);

        $oldExpiry = $this->license1->expires_at;
        $licService->renewLicense($this->license1, 365);

        $this->assertTrue($this->license1->fresh()->expires_at->isAfter($oldExpiry));
        $this->assertEquals('active', $this->license1->fresh()->status);
    }

    public function test_tenant_license_isolation_works()
    {
        TenantContext::setActiveBranchId($this->branch1->id);
        $sub1 = app(SubscriptionService::class)->getSubscription();
        $this->assertEquals($this->branch1->id, $sub1->branch_id);

        TenantContext::setActiveBranchId($this->branch2->id);
        $sub2 = app(SubscriptionService::class)->getSubscription();
        $this->assertNull($sub2);

        TenantContext::clear();
    }
}
