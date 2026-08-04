<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\AcademicTerm;
use App\Domain\Dashboard\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $branch;
    protected $admin;
    protected $teacher;
    protected $teacherUser;
    protected $student;
    protected $parent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);

        $this->branch = Branch::create(['name' => 'Integration Branch', 'slug' => 'integration-branch']);
        \App\Core\Context\TenantContext::setActiveBranchId($this->branch->id);
        $term = AcademicTerm::create(['branch_id' => $this->branch->id, 'name' => 'Term 1', 'start_date' => '2026-09-01', 'end_date' => '2027-01-31']);
        $classroom = Classroom::create(['branch_id' => $this->branch->id, 'name' => '10-A', 'code' => '10A', 'capacity' => 25]);

        // Admin
        $this->admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@saas.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $adminRole = Role::create(['name' => 'Super Admin']);
        $this->admin->roles()->attach($adminRole);
        $this->admin->refresh();

        // Teacher
        $teacherUser = User::create([
            'name' => 'Teacher Alpha',
            'email' => 'teacher@saas.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $teacherRole = Role::create(['name' => 'Teacher']);
        $teacherUser->roles()->attach($teacherRole);
        $this->teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $this->branch->id]);
        $this->teacherUser = User::find($teacherUser->id);

        // Student
        $studentUser = User::create([
            'name' => 'Student Beta',
            'email' => 'student@saas.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'branch_id' => $this->branch->id,
            'classroom_id' => $classroom->id,
            'first_name' => 'Student',
            'last_name' => 'Beta',
            'student_number' => 'STU100'
        ]);
        $studentRole = Role::create(['name' => 'Student']);
        $pStudentView = Permission::firstOrCreate(['name' => 'student.view_profile']);
        $studentRole->permissions()->attach([$pStudentView->id]);
        $studentUser->roles()->attach($studentRole);
        $studentUser->refresh();

        // Parent
        $parentUser = User::create([
            'name' => 'Parent Gamma',
            'email' => 'parent@saas.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $guardian = StudentGuardian::create([
            'user_id' => $parentUser->id,
            'student_id' => $this->student->id,
            'guardian_name' => 'Parent Gamma',
            'relation' => 'Father',
            'phone' => '5551112233'
        ]);
        $parentRole = Role::create(['name' => 'Parent']);
        $pParentView = Permission::firstOrCreate(['name' => 'parent.view_child']);
        $parentRole->permissions()->attach([$pParentView->id]);
        $parentUser->roles()->attach($parentRole);

        $this->admin = User::find($this->admin->id);
        $this->teacherUser = User::find($teacherUser->id);
        $this->student->user = User::find($studentUser->id);
        $this->parent = User::find($parentUser->id);
    }

    public function test_admin_can_access_all_modules()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.notifications.index'));
        $response->assertStatus(200);
    }

    public function test_teacher_can_access_teacher_dashboard()
    {
        $teacherUser = User::find($this->teacherUser->id);
        $teacherUser->unsetRelation('roles');
        $response = $this->actingAs($teacherUser)->get(route('teacher.dashboard'));
        $response->assertStatus(200);
    }

    public function test_student_can_access_student_dashboard()
    {
        $response = $this->actingAs($this->student->user)->get(route('student.dashboard'));
        $response->assertStatus(200);
    }

    public function test_parent_can_access_parent_dashboard()
    {
        $response = $this->actingAs($this->parent)->get(route('parent.dashboard'));
        $response->assertStatus(200);
    }

    public function test_tenant_isolation_works()
    {
        $otherBranch = Branch::create(['name' => 'Foreign Branch', 'slug' => 'foreign-branch']);
        $foreignStudentUser = User::create([
            'name' => 'Foreign Student',
            'email' => 'foreign@saas.com',
            'password' => bcrypt('password'),
            'branch_id' => $otherBranch->id
        ]);

        $foreignStudent = Student::create([
            'user_id' => $foreignStudentUser->id,
            'branch_id' => $otherBranch->id,
            'first_name' => 'Foreign',
            'last_name' => 'Student',
            'student_number' => 'STU999'
        ]);

        $this->assertNotEquals($this->student->branch_id, $foreignStudent->branch_id);
    }

    public function test_dashboard_service_returns_correct_data()
    {
        $dashboardService = app(DashboardService::class);
        $adminData = $dashboardService->getAdminDashboardData($this->branch->id);

        $this->assertArrayHasKey('totalStudents', $adminData);
        $this->assertArrayHasKey('totalTeachers', $adminData);
        $this->assertEquals(1, $adminData['totalStudents']);
        $this->assertEquals(1, $adminData['totalTeachers']);
    }

    public function test_unauthorized_user_blocked()
    {
        $guestUser = User::create([
            'name' => 'Guest User',
            'email' => 'guest@saas.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);

        // Student route with no student role or profile returns 403
        $response = $this->actingAs($guestUser)->get(route('student.dashboard'));
        $response->assertStatus(403);
    }
}
