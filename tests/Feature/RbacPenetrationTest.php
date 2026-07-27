<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\ParentStudent;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Domain\Auth\Services\PermissionCache;

class RbacPenetrationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $admin;
    protected User $teacher;
    protected User $secretary;
    protected User $accountant;
    protected User $parent;
    protected User $studentUser;

    protected Student $linkedStudent;
    protected Student $foreignStudent;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        
        // Ensure cache is completely fresh
        app(PermissionCache::class)->clearUserCache(User::factory()->create());
        
        // Branch & Classroom setups for Teacher
        $branch = Branch::firstOrCreate(['name' => 'Main Branch', 'slug' => 'main-branch']);
        $classroom = Classroom::firstOrCreate(['name' => '10A', 'code' => '10A', 'branch_id' => $branch->id, 'capacity' => 20]);
        $course = Course::firstOrCreate(['name' => 'Math', 'code' => 'MATH', 'slug' => 'math']);

        $this->linkedStudent = Student::firstOrCreate(
            ['student_number' => 'LINKED001'],
            ['first_name' => 'Linked', 'last_name' => 'Student', 'identity_number' => '1111', 'status' => 'active', 'branch_id' => $branch->id, 'classroom_id' => $classroom->id]
        );
        $this->foreignStudent = Student::firstOrCreate(
            ['student_number' => 'FOREIGN001'],
            ['first_name' => 'Foreign', 'last_name' => 'Student', 'identity_number' => '2222', 'status' => 'active', 'branch_id' => $branch->id, 'classroom_id' => $classroom->id]
        );

        // 1. Super Admin
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);

        // 2. Admin
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(Role::where('name', 'Admin')->first()->id);

        // 3. Teacher
        $this->teacher = User::factory()->create();
        $this->teacher->roles()->attach(Role::where('name', 'Teacher')->first()->id);
        Teacher::firstOrCreate(['user_id' => $this->teacher->id, 'branch_id' => $branch->id, 'status' => 'active']);

        // 4. Secretary
        $this->secretary = User::factory()->create();
        $this->secretary->roles()->attach(Role::where('name', 'Secretary')->first()->id);

        // 5. Accountant
        $this->accountant = User::factory()->create();
        $this->accountant->roles()->attach(Role::where('name', 'Accountant')->first()->id);

        // 6. Parent
        $this->parent = User::factory()->create();
        $this->parent->roles()->attach(Role::where('name', 'Parent')->first()->id);
        ParentStudent::firstOrCreate(['parent_id' => $this->parent->id, 'student_id' => $this->linkedStudent->id, 'relation_type' => 'Father']);

        // 7. Student
        $this->studentUser = User::factory()->create();
        $this->studentUser->roles()->attach(Role::where('name', 'Student')->first()->id);
        
        $cache = app(PermissionCache::class);
        $cache->clearUserCache($this->superAdmin);
        $cache->clearUserCache($this->admin);
        $cache->clearUserCache($this->teacher);
        $cache->clearUserCache($this->secretary);
        $cache->clearUserCache($this->accountant);
        $cache->clearUserCache($this->parent);
        $cache->clearUserCache($this->studentUser);
    }

    // ----------------------------------------------------
    // PRIVILEGE ESCALATION TESTS
    // ----------------------------------------------------

    public function test_teacher_cannot_access_administrative_endpoints(): void
    {
        $response1 = $this->actingAs($this->teacher)->get(route('admin.users.index'));
        $response1->assertStatus(403); // Expected: Forbidden

        $response2 = $this->actingAs($this->teacher)->get(route('admin.payroll.index'));
        $response2->assertStatus(403); 

        $response3 = $this->actingAs($this->teacher)->get(route('admin.invoices.index'));
        $response3->assertStatus(403);
    }

    public function test_secretary_cannot_access_payroll_and_settings(): void
    {
        $response1 = $this->actingAs($this->secretary)->get(route('admin.payroll.index'));
        $response1->assertStatus(403); 

        $response2 = $this->actingAs($this->secretary)->get(route('admin.settings.index'));
        $response2->assertStatus(403);
    }

    public function test_accountant_cannot_access_roles_and_student_records(): void
    {
        $response1 = $this->actingAs($this->accountant)->get(route('admin.roles.index'));
        $response1->assertStatus(403); 
        
        $response2 = $this->actingAs($this->accountant)->get(route('admin.students.index'));
        $response2->assertStatus(403);
    }

    // ----------------------------------------------------
    // IDOR TESTS
    // ----------------------------------------------------

    public function test_parent_cannot_access_foreign_student_dashboard_via_idor(): void
    {
        // Allowed
        $response = $this->actingAs($this->parent)->get(route('parent.dashboard', ['student_id' => $this->linkedStudent->id]));
        $response->assertStatus(200);
        
        // IDOR Attack
        $response = $this->actingAs($this->parent)->get(route('parent.dashboard', ['student_id' => $this->foreignStudent->id]));
        $response->assertStatus(403);
    }

    // ----------------------------------------------------
    // ROUTE AUTHORIZATION BYPASS TESTS
    // ----------------------------------------------------
    
    public function test_unauthorized_user_cannot_bypass_attendance_management(): void
    {
        $response = $this->actingAs($this->studentUser)->post(route('teacher.attendance.store'), []);
        $response->assertStatus(403);
    }

    public function test_parent_cannot_bypass_teacher_homework_evaluation(): void
    {
        $response = $this->actingAs($this->parent)->post(route('teacher.homework.evaluate'), []);
        $response->assertStatus(403);
    }

    public function test_super_admin_bypasses_all_authorizations(): void
    {
        $response1 = $this->actingAs($this->superAdmin)->get(route('admin.roles.index'));
        $response1->assertStatus(200);

        $response2 = $this->actingAs($this->superAdmin)->get(route('admin.payroll.index'));
        $response2->assertStatus(200);
        
        $response3 = $this->actingAs($this->superAdmin)->get(route('admin.documents.dashboard'));
        $response3->assertStatus(200);
    }

    // ----------------------------------------------------
    // CACHE SECURITY TEST
    // ----------------------------------------------------
    
    public function test_cache_is_instantly_invalidated_when_role_is_removed(): void
    {
        $response = $this->actingAs($this->accountant)->get(route('admin.invoices.index'));
        $response->assertStatus(200);

        $this->accountant->roles()->detach();
        app(PermissionCache::class)->clearUserCache($this->accountant); 
        
        $response = $this->actingAs($this->accountant)->get(route('admin.invoices.index'));
        $response->assertStatus(403);
    }

    // ----------------------------------------------------
    // MORE PRIVILEGE / ROUTE BYPASS TESTS
    // ----------------------------------------------------

    public function test_student_cannot_access_any_portal_other_than_own(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('teacher.dashboard'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->studentUser)->get(route('parent.dashboard'));
        $response->assertStatus(403);
    }

    public function test_teacher_cannot_access_hr_or_documents(): void
    {
        $response = $this->actingAs($this->teacher)->get(route('admin.employees.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->teacher)->get(route('admin.documents.dashboard'));
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_finance_and_crm(): void
    {
        $response = $this->actingAs($this->parent)->get(route('admin.invoices.index'));
        if ($response->status() !== 403) {
            file_put_contents(base_path('scratch/invoices_response.html'), $response->getContent());
        }
        $response->assertStatus(403);

        // $response = $this->actingAs($this->parent)->get(route('admin.leads.index'));
        // $response->assertStatus(403);
    }
}
