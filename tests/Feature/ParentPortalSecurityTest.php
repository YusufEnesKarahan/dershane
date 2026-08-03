<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Branch;
use App\Models\Role;
use Database\Seeders\RolesAndPermissionsSeeder;

class ParentPortalSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Event::fake([\App\Events\Notifications\StudentRegistered::class]);
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_parent_can_see_own_child()
    {
        $branch = Branch::create(['name' => 'Main Branch', 'tenant_id' => 'MAIN', 'slug' => 'main']);

        $parentUser = User::factory()->create(['branch_id' => $branch->id]);
        $parentRole = Role::where('name', 'Parent')->first();
        $parentUser->roles()->attach($parentRole);

        $child = Student::create([
            'student_number' => '12345',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'branch_id' => $branch->id,
            'status' => 'Active'
        ]);

        StudentGuardian::create([
            'student_id' => $child->id,
            'guardian_name' => 'Jane Doe',
            'relation' => 'Mother',
            'phone' => '1234567890',
            'user_id' => $parentUser->id
        ]);

        $response = $this->actingAs($parentUser)->get('/parent/dashboard');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('12345');
    }

    public function test_parent_cannot_see_other_child()
    {
        $branch = Branch::create(['name' => 'Main Branch', 'tenant_id' => 'MAIN', 'slug' => 'main']);

        $parentUser = User::factory()->create(['branch_id' => $branch->id]);
        $parentRole = Role::where('name', 'Parent')->first();
        $parentUser->roles()->attach($parentRole);

        $ownChild = Student::create([
            'student_number' => '12345',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'branch_id' => $branch->id,
            'status' => 'Active'
        ]);

        StudentGuardian::create([
            'student_id' => $ownChild->id,
            'guardian_name' => 'Jane Doe',
            'relation' => 'Mother',
            'phone' => '1234567890',
            'user_id' => $parentUser->id
        ]);

        $otherChild = Student::create([
            'student_number' => '99999',
            'first_name' => 'Other',
            'last_name' => 'Student',
            'branch_id' => $branch->id,
            'status' => 'Active'
        ]);

        $response = $this->actingAs($parentUser)->get('/parent/dashboard');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Other Student');
        $response->assertDontSee('99999');
    }

    public function test_student_can_see_own_profile()
    {
        $branch = Branch::create(['name' => 'Main Branch', 'tenant_id' => 'MAIN', 'slug' => 'main']);

        $studentUser = User::factory()->create(['branch_id' => $branch->id]);
        $studentRole = Role::where('name', 'Student')->first();
        $studentUser->roles()->attach($studentRole);

        $student = Student::create([
            'student_number' => '54321',
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'branch_id' => $branch->id,
            'status' => 'Active',
            'user_id' => $studentUser->id
        ]);

        $response = $this->actingAs($studentUser)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Alice Smith');
        $response->assertSee('54321');
    }

    public function test_tenant_isolation_on_parent_portal()
    {
        $branch1 = Branch::create(['name' => 'Branch 1', 'tenant_id' => 'BR1', 'slug' => 'branch-1']);
        $branch2 = Branch::create(['name' => 'Branch 2', 'tenant_id' => 'BR2', 'slug' => 'branch-2']);

        $parentUser = User::factory()->create(['branch_id' => $branch1->id]);
        $parentRole = Role::where('name', 'Parent')->first();
        $parentUser->roles()->attach($parentRole);

        // Child in Branch 2 (should not be accessible to Parent in Branch 1 even if somehow linked, though linking shouldn't happen)
        $child = Student::create([
            'student_number' => '11111',
            'first_name' => 'Isolated',
            'last_name' => 'Child',
            'branch_id' => $branch2->id,
            'status' => 'Active'
        ]);

        StudentGuardian::create([
            'student_id' => $child->id,
            'guardian_name' => 'Isolated Parent',
            'relation' => 'Mother',
            'phone' => '1111111111',
            'user_id' => $parentUser->id
        ]);

        $response = $this->actingAs($parentUser)->get('/parent/dashboard');

        $response->assertStatus(200);
        // Because of TenantScope on Student, the query to get children will filter out branch2 students for a branch1 user
        $response->assertDontSee('Isolated Child');
    }

    public function test_rbac_enforced_on_parent_portal()
    {
        $branch = Branch::create(['name' => 'Main Branch', 'tenant_id' => 'MAIN', 'slug' => 'main']);

        // User WITHOUT Parent role
        $teacherUser = User::factory()->create(['branch_id' => $branch->id]);
        $teacherRole = Role::where('name', 'Teacher')->first();
        $teacherUser->roles()->attach($teacherRole);

        $response = $this->actingAs($teacherUser)->get('/parent/dashboard');

        // Should be forbidden because of role middleware
        $response->assertStatus(403);
    }
}
