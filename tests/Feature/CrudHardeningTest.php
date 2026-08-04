<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $branchUser;
    protected $testTeacherUser;
    protected $testStudentUser;
    protected $testParentUser;
    protected $testBranch;
    protected $testClassroom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOnboardingCompleted::class,
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Fetch roles
        $rSuperAdmin = Role::where('name', 'Super Admin')->first();
        $rBranchAdmin = Role::where('name', 'Branch Admin')->first();
        $rTeacher = Role::where('name', 'Teacher')->first();
        $rStudent = Role::where('name', 'Student')->first();
        $rParent = Role::where('name', 'Parent')->first();

        // Create branch
        $this->testBranch = Branch::create([
            'name' => 'Test Branch',
            'slug' => 'test-branch',
            'phone' => '5551234567',
            'email' => 'branch@test.com',
            'address' => 'Test Address'
        ]);

        // Create users
        $this->adminUser = User::factory()->create(['branch_id' => $this->testBranch->id]);
        $this->adminUser->roles()->sync([$rSuperAdmin->id]);

        $this->branchUser = User::factory()->create(['branch_id' => $this->testBranch->id]);
        $this->branchUser->roles()->sync([$rBranchAdmin->id]);

        $this->testTeacherUser = User::factory()->create(['branch_id' => $this->testBranch->id]);
        $this->testTeacherUser->roles()->sync([$rTeacher->id]);

        $this->testStudentUser = User::factory()->create(['branch_id' => $this->testBranch->id]);
        $this->testStudentUser->roles()->sync([$rStudent->id]);

        $this->testParentUser = User::factory()->create(['branch_id' => $this->testBranch->id]);
        $this->testParentUser->roles()->sync([$rParent->id]);

        // Create classroom
        $this->testClassroom = Classroom::create([
            'branch_id' => $this->testBranch->id,
            'name' => '10-A',
            'code' => '10A',
            'capacity' => 30,
            'is_active' => true
        ]);
    }

    public function test_student_creation_requires_mandatory_fields_and_prevents_duplicates()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->testBranch->id]);

        // Validation failure for empty request
        $response = $this->from(route('admin.students.create'))->post(route('admin.students.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['student_number', 'first_name', 'last_name']);

        // Successful creation
        $validData = [
            'student_number' => 'STU-001',
            'identity_number' => '12345678901',
            'first_name' => 'Ahmet',
            'last_name' => 'Yılmaz',
            'classroom_id' => $this->testClassroom->id
        ];

        $response = $this->post(route('admin.students.store'), $validData);
        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'student_number' => 'STU-001',
            'first_name' => 'Ahmet',
            'last_name' => 'Yılmaz'
        ]);

        // Duplicate student_number validation
        $duplicateResponse = $this->from(route('admin.students.create'))->post(route('admin.students.store'), $validData);
        $duplicateResponse->assertSessionHasErrors(['student_number']);
    }

    public function test_teacher_creation_validates_email_and_limits()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->testBranch->id]);

        $data = [
            'first_name' => 'Mehmet',
            'last_name' => 'Kaya',
            'email' => 'mehmet.kaya@test.com',
            'status' => 'Active'
        ];

        $response = $this->from(route('admin.teachers.create'))->post(route('admin.teachers.store'), $data);
        $response->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'mehmet.kaya@test.com']);

        // Duplicate email validation
        $duplicateResponse = $this->from(route('admin.teachers.create'))->post(route('admin.teachers.store'), $data);
        $duplicateResponse->assertSessionHasErrors(['email']);
    }

    public function test_classroom_creation_prevents_duplicate_names_in_same_branch()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->testBranch->id]);

        $response = $this->from(route('admin.classrooms.create'))->post(route('admin.classrooms.store'), [
            'name' => '10-A',
            'code' => '10A-DUP',
            'capacity' => 25
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_exam_results_prevents_negative_scores()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->testBranch->id]);

        $exam = Exam::create([
            'branch_id' => $this->testBranch->id,
            'title' => 'Genel Deneme 1',
            'type' => 'mock_exam',
            'exam_date' => now()->toDateString(),
            'total_score' => 500,
            'status' => 'published',
            'created_by' => $this->adminUser->id
        ]);

        $student = Student::create([
            'branch_id' => $this->testBranch->id,
            'student_number' => 'STU-002',
            'first_name' => 'Ayşe',
            'last_name' => 'Demir'
        ]);

        // Negative score validation
        $response = $this->from(route('admin.exams.results', $exam->id))->post(route('admin.exams.results.store', $exam->id), [
            'student_id' => $student->id,
            'score' => -50,
            'correct_answers' => 10,
            'wrong_answers' => 5,
            'empty_answers' => 0
        ]);

        $response->assertSessionHasErrors(['score']);
    }

    public function test_unauthorized_roles_are_forbidden_from_admin_crud()
    {
        $this->actingAs($this->testStudentUser)
             ->withSession(['active_branch_id' => $this->testBranch->id]);

        $response = $this->get(route('admin.students.index'));
        $this->assertTrue(in_array($response->status(), [403, 302]));

        $response = $this->get(route('admin.teachers.index'));
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }
}
