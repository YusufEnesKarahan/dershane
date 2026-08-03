<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\License;
use App\Enums\UserStatus;
use App\Core\Context\TenantContext;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([\App\Http\Middleware\CheckOnboardingStatus::class]);

        $this->artisan('db:seed', ['--class' => 'RBACSeeder']);

        $this->tenantA = Branch::factory()->create(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        $this->tenantB = Branch::factory()->create(['name' => 'Tenant B', 'slug' => 'tenant-b']);

        // Roles
        $tenantAdminRole = Role::where('name', 'tenant_admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        // Users
        $this->adminA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->adminA->roles()->attach($tenantAdminRole->id);

        $this->staffA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->staffA->roles()->attach($staffRole->id);

        $this->teacherA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->teacherA->roles()->attach($teacherRole->id);

        $this->adminB = User::factory()->create(['branch_id' => $this->tenantB->id, 'status' => UserStatus::ACTIVE]);
        $this->adminB->roles()->attach($tenantAdminRole->id);

        // Subscription (Tenant A Limit = 2)
        $plan = Plan::create([
            'name' => 'Mini Plan',
            'slug' => 'mini-plan',
            'code' => 'MINI_PLAN',
            'price_monthly' => 100,
            'max_students' => 2,
            'max_teachers' => 2,
        ]);
        
        $license = License::create([
            'branch_id' => $this->tenantA->id,
            'license_key' => 'TEST-KEY-A',
            'status' => 'active',
            'expires_at' => now()->addDays(30),
            'max_students' => 2,
            'max_teachers' => 2,
        ]);

        Subscription::create([
            'branch_id' => $this->tenantA->id,
            'plan_id' => $plan->id,
            'license_id' => $license->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);
        
        // Subscription (Tenant B Limit = 100)
        $planB = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'code' => 'PRO_PLAN',
            'price_monthly' => 500,
            'max_students' => 100,
            'max_teachers' => 20,
        ]);
        
        $licenseB = License::create([
            'branch_id' => $this->tenantB->id,
            'license_key' => 'TEST-KEY-B',
            'status' => 'active',
            'expires_at' => now()->addDays(30),
            'max_students' => 100,
            'max_teachers' => 20,
        ]);

        Subscription::create([
            'branch_id' => $this->tenantB->id,
            'plan_id' => $planB->id,
            'license_id' => $licenseB->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);
    }

    public function test_tenant_admin_can_create_student()
    {
        TenantContext::setActiveBranchId($this->adminA->branch_id);
        $response = $this->actingAs($this->adminA)->post(route('admin.students.store'), [
            'student_number' => 'STU-001',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'status' => 'Active',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('students', [
            'student_number' => 'STU-001',
            'branch_id' => $this->tenantA->id,
        ]);
    }

    public function test_staff_can_create_student()
    {
        TenantContext::setActiveBranchId($this->staffA->branch_id);
        $response = $this->actingAs($this->staffA)->post(route('admin.students.store'), [
            'student_number' => 'STU-002',
            'first_name' => 'Staff',
            'last_name' => 'Student',
            'status' => 'Active',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('students', [
            'student_number' => 'STU-002',
            'branch_id' => $this->tenantA->id,
        ]);
    }

    public function test_teacher_cannot_create_student()
    {
        TenantContext::setActiveBranchId($this->teacherA->branch_id);
        $response = $this->actingAs($this->teacherA)->post(route('admin.students.store'), [
            'student_number' => 'STU-003',
            'first_name' => 'Teacher',
            'last_name' => 'Student',
            'status' => 'Active',
        ]);

        $response->assertStatus(403);
    }

    public function test_tenant_isolation_for_students()
    {
        $studentA = Student::create([
            'branch_id' => $this->tenantA->id,
            'student_number' => 'T-A-1',
            'first_name' => 'A1',
            'last_name' => 'Test',
        ]);

        TenantContext::setActiveBranchId($this->adminB->branch_id);
        $response = $this->actingAs($this->adminB)->get(route('admin.students.show', $studentA->id));
        $response->assertStatus(404);
    }

    public function test_subscription_limit_blocks_creation()
    {
        Student::create(['branch_id' => $this->tenantA->id, 'student_number' => 'T-A-1', 'first_name' => 'A1', 'last_name' => 'Test']);
        Student::create(['branch_id' => $this->tenantA->id, 'student_number' => 'T-A-2', 'first_name' => 'A2', 'last_name' => 'Test']);

        TenantContext::setActiveBranchId($this->adminA->branch_id);
        $response = $this->actingAs($this->adminA)->post(route('admin.students.store'), [
            'student_number' => 'T-A-3',
            'first_name' => 'A3',
            'last_name' => 'Test',
            'status' => 'Active',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('students', ['student_number' => 'T-A-3']);
    }

    public function test_soft_delete_works()
    {
        $student = Student::create([
            'branch_id' => $this->tenantA->id,
            'student_number' => 'T-A-1',
            'first_name' => 'A1',
            'last_name' => 'Test',
        ]);

        TenantContext::setActiveBranchId($this->adminA->branch_id);
        $response = $this->actingAs($this->adminA)->delete(route('admin.students.destroy', $student->id));
        
        $response->assertStatus(302);
        
        $this->assertSoftDeleted('students', [
            'id' => $student->id
        ]);
    }
}
