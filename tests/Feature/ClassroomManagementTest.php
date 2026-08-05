<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\ClassroomType;
use App\Models\Student;
use App\Models\Plan;
use App\Models\License;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClassroomManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $tenantAdmin;
    protected User $staffUser;
    protected User $teacherUser;
    protected Branch $branch1;
    protected Branch $branch2;
    protected ClassroomType $classroomType;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([\App\Http\Middleware\CheckOnboardingStatus::class]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->branch1 = Branch::create(['name' => 'Main Branch 1', 'slug' => 'main-branch-1', 'is_active' => true]);
        $this->branch2 = Branch::create(['name' => 'Branch 2', 'slug' => 'branch-2', 'is_active' => true]);

        $this->classroomType = ClassroomType::create([
            'name' => 'Standart Sınıf',
            'slug' => 'standart-sinif'
        ]);

        // Create Tenant Admin
        $this->tenantAdmin = User::factory()->create(['branch_id' => $this->branch1->id]);
        $tenantAdminRole = Role::firstOrCreate(['name' => 'Tenant Admin']);
        $this->tenantAdmin->roles()->attach($tenantAdminRole->id);
        $permissions = \App\Models\Permission::where('name', 'like', 'classrooms.%')->pluck('id');
        if ($permissions->isEmpty()) {
            \App\Models\Permission::create(['name' => 'classrooms.view', 'module' => 'classrooms']);
            \App\Models\Permission::create(['name' => 'classrooms.manage', 'module' => 'classrooms']);
            $permissions = \App\Models\Permission::where('name', 'like', 'classrooms.%')->pluck('id');
        }
        $tenantAdminRole->permissions()->syncWithoutDetaching($permissions);

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
            'max_classrooms' => 5,
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

    public function test_tenant_admin_can_create_classroom()
    {
        $this->actingAs($this->tenantAdmin);
        session(['active_branch_id' => $this->branch1->id]);

        $teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'branch_id' => $this->branch1->id,
            'title' => 'Matematik Öğretmeni',
        ]);

        $response = $this->post(route('admin.classrooms.store'), [
            'name' => '12-A Sayısal',
            'code' => '12A-SAY',
            'classroom_type_id' => $this->classroomType->id,
            'capacity' => 20,
            'teacher_id' => $teacher->id,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionMissing('error');
        
        $this->assertDatabaseHas('classrooms', [
            'name' => '12-A Sayısal',
            'branch_id' => $this->branch1->id,
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_teacher_cannot_create_new_classroom()
    {
        $this->actingAs($this->teacherUser);
        session(['active_branch_id' => $this->branch1->id]);

        $response = $this->post(route('admin.classrooms.store'), [
            'name' => '11-B Sözel',
            'capacity' => 15,
        ]);

        $response->assertForbidden();
    }

    public function test_tenant_isolation_works_for_classrooms()
    {
        // Admin in Branch 2 trying to view Branch 1 classroom
        $admin2 = User::factory()->create(['branch_id' => $this->branch2->id]);
        $tenantAdminRole = Role::firstOrCreate(['name' => 'Tenant Admin']);
        $admin2->roles()->attach($tenantAdminRole->id);
        $permissions = \App\Models\Permission::where('name', 'like', 'classrooms.%')->pluck('id');
        $tenantAdminRole->permissions()->syncWithoutDetaching($permissions);

        $classroom1 = Classroom::create([
            'name' => '10-A',
            'code' => '10A',
            'branch_id' => $this->branch1->id,
            'capacity' => 30,
        ]);

        $this->actingAs($admin2);
        session(['active_branch_id' => $this->branch2->id]);

        $response = $this->get(route('admin.classrooms.show', $classroom1->id));
        $response->assertForbidden();
    }

    public function test_classroom_limit_blocks_creation()
    {
        // Fill up to 5 classrooms (max_classrooms = 5)
        for ($i = 0; $i < 5; $i++) {
            Classroom::create([
                'name' => 'Class ' . $i,
                'code' => 'CLS-' . $i,
                'branch_id' => $this->branch1->id,
                'capacity' => 20,
            ]);
        }

        $this->actingAs($this->tenantAdmin);
        session(['active_branch_id' => $this->branch1->id]);

        $response = $this->post(route('admin.classrooms.store'), [
            'name' => 'Limit Class',
            'capacity' => 20,
        ]);

        $response->assertSessionHas('error'); // Limit ulaştı
        $this->assertDatabaseMissing('classrooms', ['name' => 'Limit Class']);
    }

    public function test_can_attach_and_detach_students()
    {
        $this->actingAs($this->tenantAdmin);
        session(['active_branch_id' => $this->branch1->id]);

        $classroom = Classroom::create([
            'name' => '10-A',
            'code' => '10A-2',
            'branch_id' => $this->branch1->id,
            'capacity' => 30,
        ]);

        // Create 2 students for branch1
        $studentUser1 = User::factory()->create(['branch_id' => $this->branch1->id]);
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'branch_id' => $this->branch1->id,
            'student_number' => '1001',
            'first_name' => 'Test',
            'last_name' => 'Student 1',
        ]);

        $studentUser2 = User::factory()->create(['branch_id' => $this->branch1->id]);
        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'branch_id' => $this->branch1->id,
            'student_number' => '1002',
            'first_name' => 'Test',
            'last_name' => 'Student 2',
        ]);

        // Attach Student 1
        $response = $this->post(route('admin.classrooms.students.attach', $classroom->id), [
            'student_ids' => [$student1->id],
        ]);
        $response->assertRedirect();
        
        $this->assertDatabaseHas('classroom_student', [
            'student_id' => $student1->id,
            'classroom_id' => $classroom->id,
        ]);

        // Detach Student 1
        $response = $this->post(route('admin.classrooms.students.detach', $classroom->id), [
            'student_ids' => [$student1->id],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseMissing('classroom_student', [
            'student_id' => $student1->id,
            'classroom_id' => $classroom->id,
        ]);
    }
}
