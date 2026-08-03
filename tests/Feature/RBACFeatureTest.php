<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Classroom;
use App\Enums\UserStatus;

class RBACFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RBACSeeder']);

        $this->tenantA = Branch::factory()->create(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        $this->tenantB = Branch::factory()->create(['name' => 'Tenant B', 'slug' => 'tenant-b']);

        // Roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $tenantAdminRole = Role::where('name', 'tenant_admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();

        // Users
        $this->superAdmin = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $this->superAdmin->roles()->attach($superAdminRole->id);

        $this->tenantAdminA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->tenantAdminA->roles()->attach($tenantAdminRole->id);

        $this->tenantAdminB = User::factory()->create(['branch_id' => $this->tenantB->id, 'status' => UserStatus::ACTIVE]);
        $this->tenantAdminB->roles()->attach($tenantAdminRole->id);

        $this->teacherA = User::factory()->create(['branch_id' => $this->tenantA->id, 'status' => UserStatus::ACTIVE]);
        $this->teacherA->roles()->attach($teacherRole->id);

        $this->studentA = Student::create([
            'branch_id' => $this->tenantA->id,
            'user_id' => $this->teacherA->id,
            'student_number' => 'STU-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'status' => 'active',
            'payment_type' => 'monthly',
        ]);
        $this->studentB = Student::create([
            'branch_id' => $this->tenantB->id,
            'user_id' => $this->superAdmin->id,
            'student_number' => 'STU-002',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'status' => 'active',
            'payment_type' => 'monthly',
        ]);
        
        $this->classA = Classroom::create([
            'branch_id' => $this->tenantA->id,
            'name' => 'Class A',
            'code' => 'CLS-001',
            'capacity' => 20,
            'status' => 'active',
        ]);
    }

    public function test_tenant_admin_redirected_to_dashboard()
    {
        $response = $this->post('/login', [
            'email' => $this->tenantAdminA->email,
            'password' => 'password',
        ]);
        
        $response->assertRedirect(route('tenant.dashboard'));
        $this->assertAuthenticatedAs($this->tenantAdminA);
    }

    public function test_super_admin_redirected_to_admin_dashboard()
    {
        $response = $this->post('/login', [
            'email' => $this->superAdmin->email,
            'password' => 'password',
        ]);
        
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->superAdmin);
    }

    public function test_inactive_user_cannot_login()
    {
        $inactiveUser = User::factory()->create(['status' => UserStatus::PASSIVE, 'password' => bcrypt('password')]);
        
        $response = $this->post('/login', [
            'email' => $inactiveUser->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_tenant_admin_can_view_own_student()
    {
        // Tenant A admin can view Student A
        $this->assertTrue($this->tenantAdminA->can('view', $this->studentA));
    }

    public function test_tenant_admin_cannot_view_other_tenant_student()
    {
        // Tenant A admin CANNOT view Student B
        $this->assertFalse($this->tenantAdminA->can('view', $this->studentB));
    }

    public function test_teacher_can_view_classes()
    {
        $this->assertTrue($this->teacherA->can('view', $this->classA));
    }

    public function test_super_admin_can_view_any_student()
    {
        $this->assertTrue($this->superAdmin->can('view', $this->studentA));
        $this->assertTrue($this->superAdmin->can('view', $this->studentB));
    }
}
