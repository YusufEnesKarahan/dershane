<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Plan;
use App\Models\License;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $tenantAdmin;
    protected User $staffUser;
    protected User $teacherUser;
    protected Branch $branch1;
    protected Branch $branch2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([\App\Http\Middleware\CheckOnboardingStatus::class]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->branch1 = Branch::create(['name' => 'Main Branch 1', 'slug' => 'main-branch-1', 'is_active' => true]);
        $this->branch2 = Branch::create(['name' => 'Branch 2', 'slug' => 'branch-2', 'is_active' => true]);

        // Create Tenant Admin
        $this->tenantAdmin = User::factory()->create(['branch_id' => $this->branch1->id]);
        $tenantAdminRole = Role::firstOrCreate(['name' => 'Tenant Admin']);
        $this->tenantAdmin->roles()->attach($tenantAdminRole->id);
        $permissions = \App\Models\Permission::where('name', 'like', 'teachers.%')->pluck('id');
        if ($permissions->isEmpty()) {
            \App\Models\Permission::create(['name' => 'teachers.view', 'module' => 'teachers']);
            \App\Models\Permission::create(['name' => 'teachers.create', 'module' => 'teachers']);
            \App\Models\Permission::create(['name' => 'teachers.update', 'module' => 'teachers']);
            \App\Models\Permission::create(['name' => 'teachers.delete', 'module' => 'teachers']);
            $permissions = \App\Models\Permission::where('name', 'like', 'teachers.%')->pluck('id');
        }
        $tenantAdminRole->permissions()->syncWithoutDetaching($permissions);

        // Create Staff
        $this->staffUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $this->staffUser->roles()->attach($staffRole->id);
        $staffRole->permissions()->syncWithoutDetaching($permissions);

        // Create Teacher
        $this->teacherUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $teacherRole = Role::where('name', 'teacher')->first() ?? Role::where('name', 'Teacher')->first();
        if ($teacherRole) {
            $this->teacherUser->roles()->attach($teacherRole->id);
        }

        // Setup Subscription Plan with limits
        $plan = Plan::create([
            'name' => 'Standard',
            'slug' => 'standard',
            'stripe_product_id' => 'prod_1',
            'stripe_price_id' => 'price_1',
            'price' => 100,
            'max_teachers' => 20,
            'is_active' => true,
        ]);

        $license = License::create([
            'license_key' => 'TEST-LICENSE-1',
            'status' => 'active',
            'branch_id' => $this->branch1->id,
            'plan_id' => $plan->id,
            'valid_until' => now()->addYear(),
        ]);

        Subscription::create([
            'license_id' => $license->id,
            'branch_id' => $this->branch1->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    public function test_tenant_admin_can_create_teacher()
    {
        $this->actingAs($this->tenantAdmin);
        
        // Tenant context switch middleware simulation
        session(['active_branch_id' => $this->branch1->id]);

        $response = $this->post(route('admin.teachers.store'), [
            'first_name' => 'Ahmet',
            'last_name' => 'Yılmaz',
            'email' => 'ahmet.teacher@test.com',
            'phone' => '05551234567',
            'title' => 'Matematik Öğretmeni',
            'status' => 'Active',
        ]);

        $response->assertRedirect();
        if (session()->has('error')) {
            dd(session('error'));
        }
        $response->assertSessionMissing('error');
        
        $this->assertDatabaseHas('users', [
            'email' => 'ahmet.teacher@test.com',
            'branch_id' => $this->branch1->id,
        ]);

        $this->assertDatabaseHas('teachers', [
            'title' => 'Matematik Öğretmeni',
            'branch_id' => $this->branch1->id,
        ]);
    }

    public function test_staff_can_create_teacher()
    {
        $this->actingAs($this->staffUser);
        session(['active_branch_id' => $this->branch1->id]);

        $response = $this->post(route('admin.teachers.store'), [
            'first_name' => 'Ayşe',
            'last_name' => 'Kaya',
            'email' => 'ayse.teacher@test.com',
            'title' => 'Fizik Öğretmeni',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'ayse.teacher@test.com']);
    }

    public function test_teacher_cannot_create_new_teacher()
    {
        $this->actingAs($this->teacherUser);
        session(['active_branch_id' => $this->branch1->id]);

        $response = $this->post(route('admin.teachers.store'), [
            'first_name' => 'Mehmet',
            'last_name' => 'Demir',
            'email' => 'mehmet.teacher@test.com',
            'title' => 'Kimya Öğretmeni',
        ]);

        $response->assertForbidden();
    }

    public function test_tenant_isolation_works()
    {
        // Admin in Branch 2 trying to view Branch 1 teacher
        $admin2 = User::factory()->create(['branch_id' => $this->branch2->id]);
        $tenantAdminRole = Role::firstOrCreate(['name' => 'Tenant Admin']);
        $admin2->roles()->attach($tenantAdminRole->id);
        $permissions = \App\Models\Permission::where('name', 'like', 'teachers.%')->pluck('id');
        $tenantAdminRole->permissions()->syncWithoutDetaching($permissions);

        $teacher1 = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'branch_id' => $this->branch1->id,
            'title' => 'Tarih',
        ]);

        $this->actingAs($admin2);
        session(['active_branch_id' => $this->branch2->id]);

        $response = $this->get(route('admin.teachers.show', $teacher1->id));
        $response->assertForbidden(); // Should be blocked by TeacherPolicy isSameTenant
    }

    public function test_teacher_limit_blocks_creation()
    {
        // Fill up to 20 teachers
        for ($i = 0; $i < 20; $i++) {
            $u = User::factory()->create(['branch_id' => $this->branch1->id]);
            Teacher::create([
                'user_id' => $u->id,
                'branch_id' => $this->branch1->id,
                'title' => 'T',
            ]);
        }

        $this->actingAs($this->tenantAdmin);
        session(['active_branch_id' => $this->branch1->id]);

        $response = $this->post(route('admin.teachers.store'), [
            'first_name' => 'Limit',
            'last_name' => 'Test',
            'email' => 'limit@test.com',
            'title' => 'Limit',
        ]);

        $response->assertSessionHas('error'); // Limit ulaştı
        $this->assertDatabaseMissing('users', ['email' => 'limit@test.com']);
    }

    public function test_soft_delete_works_for_both_user_and_teacher()
    {
        $this->actingAs($this->tenantAdmin);
        session(['active_branch_id' => $this->branch1->id]);

        $u = User::factory()->create(['branch_id' => $this->branch1->id, 'email' => 'todelete@test.com']);
        $t = Teacher::create([
            'user_id' => $u->id,
            'branch_id' => $this->branch1->id,
            'title' => 'ToDelete',
        ]);

        $response = $this->delete(route('admin.teachers.destroy', $t->id));
        $response->assertRedirect(route('admin.teachers.index'));

        $this->assertSoftDeleted('teachers', ['id' => $t->id]);
        $this->assertSoftDeleted('users', ['id' => $u->id]);
    }

    public function test_teacher_can_view_own_profile()
    {
        $teacherProfile = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'branch_id' => $this->branch1->id,
            'title' => 'Kendi Profilim',
        ]);

        $this->actingAs($this->teacherUser);
        session(['active_branch_id' => $this->branch1->id]);

        // Should be able to view own profile
        $response = $this->get(route('admin.teachers.show', $teacherProfile->id));
        $response->assertOk();

        // But cannot view another teacher's profile
        $anotherUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $anotherProfile = Teacher::create([
            'user_id' => $anotherUser->id,
            'branch_id' => $this->branch1->id,
            'title' => 'Baskasi',
        ]);

        $response2 = $this->get(route('admin.teachers.show', $anotherProfile->id));
        $response2->assertForbidden();
    }
}
