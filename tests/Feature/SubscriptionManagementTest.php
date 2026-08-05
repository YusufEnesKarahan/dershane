<?php

namespace Tests\Feature;

use App\Domain\Platform\Services\SubscriptionLimitService;
use App\Models\AcademicTerm;
use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\SystemIdentity;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdminUser;
    protected User $normalUserAccount;
    protected Branch $tenantBranch;
    protected Plan $basePlan;
    protected Plan $starterPlan;
    protected Plan $professionalPlan;

    protected function setUp(): void
    {
        parent::setUp();

        SystemIdentity::create(['company_name' => 'Test', 'product_name' => 'Test ERP']);
        AcademicTerm::create(['name' => '2025-2026', 'start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->tenantBranch = Branch::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);

        $roleSuper = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $this->superAdminUser = User::factory()->create(['branch_id' => $this->tenantBranch->id]);
        $this->superAdminUser->roles()->attach($roleSuper);

        $this->normalUserAccount = User::factory()->create(['branch_id' => $this->tenantBranch->id]);

        $this->basePlan = Plan::create([
            'name' => 'System Base',
            'slug' => 'system-base',
            'price' => 0,
            'billing_cycle' => 'monthly',
            'is_active' => true,
        ]);

        License::create([
            'license_key' => 'TEST-LICENSE-' . uniqid(),
            'status' => 'active',
            'plan_id' => $this->basePlan->id,
            'plan' => $this->basePlan->slug,
        ]);
    }

    public function test_normal_user_cannot_access_subscription_management(): void
    {
        $response = $this->actingAs($this->normalUserAccount)->get(route('admin.platform.subscriptions.index'));

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_subscriptions_index(): void
    {
        $response = $this->actingAs($this->superAdminUser)->get(route('admin.platform.subscriptions.index'));

        $response->assertStatus(200);
    }
}