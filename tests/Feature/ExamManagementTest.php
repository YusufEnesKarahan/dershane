<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Classroom;
use App\Models\Plan;
use App\Models\Subscription;
use Database\Seeders\RolesAndPermissionsSeeder;

class ExamManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(RolesAndPermissionsSeeder::class);
        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Event::fake();
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);
    }

    private function createTenantAdmin($branchId)
    {
        $admin = User::factory()->create(['branch_id' => $branchId]);
        $role = Role::where('name', 'Admin')->first();
        $admin->roles()->attach($role->id);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($admin);
        return $admin;
    }

    private function createTeacher($branchId)
    {
        $teacher = User::factory()->create(['branch_id' => $branchId]);
        $role = Role::where('name', 'Teacher')->first();
        $teacher->roles()->attach($role->id);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($teacher);
        return $teacher;
    }

    private function createStudentUser($studentModel)
    {
        $user = User::factory()->create(['branch_id' => $studentModel->branch_id]);
        $role = Role::where('name', 'Student')->first();
        $user->roles()->attach($role->id);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($user);
        $studentModel->update(['user_id' => $user->id]);
        return $user;
    }

    private function createParentUser($studentModel)
    {
        $user = User::factory()->create(['branch_id' => $studentModel->branch_id]);
        $role = Role::where('name', 'Parent')->first();
        $user->roles()->attach($role->id);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($user);
        
        $guardian = \App\Models\StudentGuardian::create([
            'user_id' => $user->id,
            'branch_id' => $studentModel->branch_id,
            'student_id' => $studentModel->id,
            'guardian_name' => 'Test Parent',
            'phone' => '123456789',
            'relation' => 'Father'
        ]);
        
        \App\Models\ParentStudent::create([
            'parent_id' => $guardian->id,
            'student_id' => $studentModel->id,
            'relation' => 'Father'
        ]);
        
        return $user;
    }

    private function setupSubscription($branch, $maxExams)
    {
        $plan = Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan-' . uniqid(),
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'price' => 100,
            'max_students' => 100,
            'limits' => ['max_exams' => $maxExams]
        ]);
        
        $license = \App\Models\License::create([
            'license_key' => uniqid(),
            'tenant_name' => $branch->name,
            'contact_email' => 'test@test.com',
            'status' => 'active',
        ]);
        
        Subscription::create([
            'branch_id' => $branch->id,
            'plan_id' => $plan->id,
            'license_id' => $license->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'active'
        ]);
    }

    public function test_tenant_admin_exam_crud()
    {
        // $this->withoutExceptionHandling();
        $branch = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        $this->setupSubscription($branch, 10);
        $admin = $this->createTenantAdmin($branch->id);
        
        $examType = ExamType::create(['branch_id' => $branch->id, 'name' => 'Deneme Sınavı']);

        $response = $this->actingAs($admin)->post(route('admin.exams.store'), [
            'title' => 'Test Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', ['title' => 'Test Exam', 'branch_id' => $branch->id]);

        $exam = Exam::first();

        $response = $this->actingAs($admin)->put(route('admin.exams.update', $exam), [
            'title' => 'Updated Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 90,
            'total_score' => 100,
            'status' => 'published'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', ['title' => 'Updated Exam']);

        $response = $this->actingAs($admin)->delete(route('admin.exams.destroy', $exam));
        $response->assertRedirect();
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }

    public function test_teacher_result_entry()
    {
        $branch = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        $this->setupSubscription($branch, 10);
        $teacher = $this->createTeacher($branch->id);

        $examType = ExamType::create(['branch_id' => $branch->id, 'name' => 'Deneme Sınavı']);
        $exam = Exam::create([
            'branch_id' => $branch->id,
            'title' => 'Test Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
            'created_by' => $teacher->id
        ]);

        $student1 = Student::create(['branch_id' => $branch->id, 'first_name' => 'S1', 'last_name' => 'L1', 'student_number' => '101']);
        $student2 = Student::create(['branch_id' => $branch->id, 'first_name' => 'S2', 'last_name' => 'L2', 'student_number' => '102']);

        $response = $this->actingAs($teacher)->post(route('admin.exams.results.store', $exam), [
            'results' => [
                ['student_id' => $student1->id, 'score' => 85, 'notes' => 'Good'],
                ['student_id' => $student2->id, 'score' => 95, 'notes' => 'Excellent'],
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exam_results', ['student_id' => $student2->id, 'score' => 95, 'rank' => 1]);
        $this->assertDatabaseHas('exam_results', ['student_id' => $student1->id, 'score' => 85, 'rank' => 2]);
    }

    public function test_teacher_cannot_access_another_classroom_exam()
    {
        // For simplicity in this SaaS architecture, branch isolation is the primary boundary.
        // We will test if a teacher from Branch B can access Branch A exam.
        $branchA = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        $branchB = Branch::create(['name' => 'Branch B', 'slug' => 'branch-b']);
        
        $teacherB = $this->createTeacher($branchB->id);
        
        $examType = ExamType::create(['branch_id' => $branchA->id, 'name' => 'Deneme Sınavı']);
        $examA = Exam::create([
            'branch_id' => $branchA->id,
            'title' => 'Test Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
            'created_by' => User::factory()->create(['branch_id' => $branchA->id])->id
        ]);

        $response = $this->actingAs($teacherB)->get(route('admin.exams.show', $examA));
        $response->assertForbidden();
    }

    public function test_tenant_isolation()
    {
        $branchA = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        $branchB = Branch::create(['name' => 'Branch B', 'slug' => 'branch-b']);
        $this->setupSubscription($branchA, 10);
        $this->setupSubscription($branchB, 10);

        $adminA = $this->createTenantAdmin($branchA->id);
        $adminB = $this->createTenantAdmin($branchB->id);

        $examTypeA = ExamType::create(['branch_id' => $branchA->id, 'name' => 'Type A']);
        $examA = Exam::create([
            'branch_id' => $branchA->id,
            'title' => 'Exam A',
            'exam_type_id' => $examTypeA->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
            'created_by' => $adminA->id
        ]);

        $response = $this->actingAs($adminB)->get(route('admin.exams.index'));
        $response->assertOk();
        $response->assertDontSee('Exam A');
    }

    public function test_subscription_max_exam_limit()
    {
        $branch = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        $this->setupSubscription($branch, 1); // Only 1 exam allowed
        $admin = $this->createTenantAdmin($branch->id);

        $examType = ExamType::create(['branch_id' => $branch->id, 'name' => 'Type']);
        
        // Create 1st exam
        $this->actingAs($admin)->post(route('admin.exams.store'), [
            'title' => 'First Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
        ])->assertRedirect();

        // Try to create 2nd exam
        $response = $this->actingAs($admin)->post(route('admin.exams.store'), [
            'title' => 'Second Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('exams', ['title' => 'Second Exam']);
    }

    public function test_student_sees_only_own_results()
    {
        $branch = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        
        $student1 = Student::create(['branch_id' => $branch->id, 'first_name' => 'Ali', 'last_name' => 'V', 'student_number' => '201']);
        $student2 = Student::create(['branch_id' => $branch->id, 'first_name' => 'Veli', 'last_name' => 'K', 'student_number' => '202']);
        
        $user1 = $this->createStudentUser($student1);
        $user2 = $this->createStudentUser($student2);

        $examType = ExamType::create(['branch_id' => $branch->id, 'name' => 'Type']);
        $exam = Exam::create([
            'branch_id' => $branch->id,
            'title' => 'Biology Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
            'created_by' => $user1->id
        ]);

        \App\Models\ExamResult::create(['branch_id' => $branch->id, 'exam_id' => $exam->id, 'student_id' => $student1->id, 'score' => 80]);
        \App\Models\ExamResult::create(['branch_id' => $branch->id, 'exam_id' => $exam->id, 'student_id' => $student2->id, 'score' => 90]);

        $response = $this->actingAs($user1)->get(route('student.dashboard'));
        $response->assertOk();
        
        // Assert student 1 view has the results variable and counts exactly 1 result (their own)
        $examResults = $response->viewData('examResults');
        $this->assertCount(1, $examResults);
        $this->assertEquals($student1->id, $examResults->first()->student_id);
    }

    public function test_parent_sees_only_own_child_results()
    {
        $branch = Branch::create(['name' => 'Branch A', 'slug' => 'branch-a']);
        
        $student1 = Student::create(['branch_id' => $branch->id, 'first_name' => 'S1', 'last_name' => 'L1', 'student_number' => '301']); // Child
        $student2 = Student::create(['branch_id' => $branch->id, 'first_name' => 'S2', 'last_name' => 'L2', 'student_number' => '302']); // Not child
        
        $parentUser = $this->createParentUser($student1);

        $examType = ExamType::create(['branch_id' => $branch->id, 'name' => 'Type']);
        $exam = Exam::create([
            'branch_id' => $branch->id,
            'title' => 'Math Exam',
            'exam_type_id' => $examType->id,
            'exam_date' => '2026-10-10',
            'duration_minutes' => 120,
            'total_score' => 100,
            'created_by' => $parentUser->id
        ]);

        \App\Models\ExamResult::create(['branch_id' => $branch->id, 'exam_id' => $exam->id, 'student_id' => $student1->id, 'score' => 88]);
        \App\Models\ExamResult::create(['branch_id' => $branch->id, 'exam_id' => $exam->id, 'student_id' => $student2->id, 'score' => 99]);

        $response = $this->actingAs($parentUser)->get(route('parent.dashboard'));
        $response->assertOk();
        
        $childrenData = $response->viewData('childrenData');
        
        $this->assertCount(1, $childrenData); // Only sees own child
        
        $childResults = $childrenData[0]['exam_results'];
        $this->assertCount(1, $childResults); // Only sees results for that child
        $this->assertEquals($student1->id, $childResults->first()->student_id);
    }
}
