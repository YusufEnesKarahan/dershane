<?php

namespace Tests\Feature;

use App\Domain\Platform\Services\SubscriptionLimitService;
use App\Models\AcademicTerm;
use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\PlatformAuditLog;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
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

        $this->starterPlan = Plan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 299,
            'billing_cycle' => 'monthly',
            'trial_days' => 7,
            'max_students' => 1,
            'max_users' => 5,
            'max_teachers' => 2,
            'is_active' => true,
            'features' => ['basic_dashboard'],
        ]);

        $this->professionalPlan = Plan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'price' => 799,
            'billing_cycle' => 'monthly',
            'trial_days' => 14,
            'max_students' => 100,
            'max_users' => 20,
            'max_teachers' => 10,
            'is_active' => true,
            'features' => ['basic_dashboard', 'advanced_reports'],
        ]);

        Student::create([
            'student_number' => 'S-1001',
            'first_name' => 'Ada',
            'last_name' => 'Yılmaz',
            'branch_id' => $this->tenantBranch->id,
            'status' => 'active',
        ]);
        Teacher::create([
            'user_id' => $this->superAdminUser->id,
            'branch_id' => $this->tenantBranch->id,
            'title' => 'Matematik Öğretmeni',
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_create_plan(): void
    {
        $response = $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.plans.store'), [
            'name' => 'Business',
            'slug' => 'business',
            'description' => 'Business plan',
            'price' => 1299,
            'billing_cycle' => 'yearly',
            'trial_days' => 21,
            'max_students' => 250,
            'max_users' => 30,
            'max_teachers' => 15,
            'is_active' => 1,
            'features' => 'reports, automation',
        ]);

        $response->assertRedirect(route('admin.platform.subscriptions.plans'));

        $this->assertDatabaseHas('plans', [
            'slug' => 'business',
            'billing_cycle' => 'yearly',
            'trial_days' => 21,
        ]);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'subscription.plan.created',
        ]);
    }

    public function test_normal_user_cannot_access_subscription_management(): void
    {
        $response = $this->actingAs($this->normalUserAccount)->get(route('admin.platform.subscriptions.index'));

        $response->assertStatus(403);
    }

    public function test_tenant_can_be_assigned_to_plan(): void
    {
        $response = $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.assign'), [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->starterPlan->id,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'trial',
        ]);

        $this->assertDatabaseHas('subscription_histories', [
            'action' => 'created',
        ]);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'subscription.assigned',
        ]);
    }

    public function test_upgrade_operation_changes_subscription_plan(): void
    {
        $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.assign'), [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->starterPlan->id,
        ]);

        $response = $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.change-plan'), [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->professionalPlan->id,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->professionalPlan->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('subscription_histories', [
            'action' => 'upgraded',
        ]);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'subscription.upgraded',
        ]);
    }

    public function test_cancel_operation_closes_subscription(): void
    {
        $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.assign'), [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->starterPlan->id,
        ]);

        $response = $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.cancel'), [
            'branch_id' => $this->tenantBranch->id,
            'reason' => 'Manual stop',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'branch_id' => $this->tenantBranch->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Manual stop',
        ]);

        $this->assertDatabaseHas('subscription_histories', [
            'action' => 'cancelled',
        ]);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'subscription.cancelled',
        ]);
    }

    public function test_limit_control_works_for_students(): void
    {
        $this->actingAs($this->superAdminUser)->post(route('admin.platform.subscriptions.assign'), [
            'branch_id' => $this->tenantBranch->id,
            'plan_id' => $this->starterPlan->id,
        ]);

        $limitService = app(SubscriptionLimitService::class);

        $this->assertFalse($limitService->canAddStudent($this->tenantBranch));
        $this->assertTrue($limitService->canAddUser($this->tenantBranch));
        $this->assertTrue($limitService->canAddTeacher($this->tenantBranch));
    }
}