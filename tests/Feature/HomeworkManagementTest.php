<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\AcademicTerm;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HomeworkManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $branch;
    protected $term;
    protected $classroom;
    protected $course;
    protected $teacher;
    protected $student;
    protected $guardian;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);
        Notification::fake();

        $this->branch = Branch::create(['name' => 'Main Branch', 'slug' => 'main-branch']);
        $this->term = AcademicTerm::create(['branch_id' => $this->branch->id, 'name' => 'Fall 2026', 'start_date' => '2026-09-01', 'end_date' => '2027-01-31']);
        $this->classroom = Classroom::create(['branch_id' => $this->branch->id, 'name' => '12-A', 'code' => '12A', 'capacity' => 20]);
        $this->course = Course::create(['branch_id' => $this->branch->id, 'name' => 'Mathematics', 'code' => 'MATH101', 'slug' => 'mathematics-math101']);
        
        $teacherUser = User::create([
            'name' => 'Math Teacher',
            'email' => 'teacher@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $this->branch->id]);
        $teacherRole = Role::create(['name' => 'Teacher']);
        $pView = \App\Models\Permission::firstOrCreate(['name' => 'homework.view']);
        $pCreate = \App\Models\Permission::firstOrCreate(['name' => 'homework.create']);
        $pUpdate = \App\Models\Permission::firstOrCreate(['name' => 'homework.update']);
        $pPublish = \App\Models\Permission::firstOrCreate(['name' => 'homework.publish']);
        $pGrade = \App\Models\Permission::firstOrCreate(['name' => 'homework.grade']);
        
        $teacherRole->permissions()->attach([$pView->id, $pCreate->id, $pUpdate->id, $pPublish->id, $pGrade->id]);
        $teacherUser->roles()->attach($teacherRole);

        $studentUser = User::create([
            'name' => 'John Doe',
            'email' => 'student@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'student_number' => 'STU2026',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        $studentRole = Role::create(['name' => 'Student']);
        $pProfile = \App\Models\Permission::firstOrCreate(['name' => 'student.view_profile']);
        $studentRole->permissions()->attach([$pProfile->id, $pView->id]);
        $studentUser->roles()->attach($studentRole);

        $guardianUser = User::create([
            'name' => 'Jane Doe',
            'email' => 'parent@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->guardian = StudentGuardian::create([
            'user_id' => $guardianUser->id,
            'student_id' => $this->student->id,
            'guardian_name' => 'Jane Doe',
            'phone' => '1234567890',
            'relation' => 'Mother'
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->attach([$pView->id, $pCreate->id, $pUpdate->id]);
        $this->admin->roles()->attach($role);
        
        $plan = Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'price' => 100,
            'interval' => 'month',
            'limits' => ['max_homeworks' => 100]
        ]);
        $license = \App\Models\License::create([
            'license_key' => 'TEST-KEY',
            'tenant_name' => $this->branch->name,
            'contact_email' => 'test@test.com',
            'status' => 'active',
        ]);
        Subscription::create([
            'branch_id' => $this->branch->id,
            'plan_id' => $plan->id,
            'license_id' => $license->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth()
        ]);
    }

    public function test_tenant_admin_crud()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.homeworks.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Math Assignment 1',
            'description' => 'Solve pages 10-15',
            'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'max_score' => 100,
            'allow_late_submission' => false,
            'status' => 'published'
        ]);

        if (session('errors')) {
            $this->fail("Validation failed: " . json_encode(session('errors')));
        }
        if (session('error')) {
            $this->fail("Session error: " . json_encode(session('error')));
        }

        $response->assertRedirect(route('admin.homeworks.index'));
        $this->assertDatabaseHas('homeworks', ['title' => 'Math Assignment 1']);
    }

    public function test_teacher_own_homework()
    {
        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Teacher Homework',
            'due_date' => now()->addDays(3),
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->teacher->user)->get(route('teacher.homeworks.show', $homework));
        $response->assertOk();
    }

    public function test_teacher_cannot_grade_other_teacher_homework()
    {
        $teacherUser2 = User::create(['name' => 'T2', 'email' => 't2@dershane.com', 'password' => bcrypt('password'), 'branch_id' => $this->branch->id]);
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'branch_id' => $this->branch->id]);
        $teacherUser2->roles()->attach($this->teacher->user->roles->first());

        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $teacher2->id,
            'title' => 'Teacher 2 Homework',
            'due_date' => now()->addDays(3),
            'status' => 'published'
        ]);

        $submission = HomeworkSubmission::create([
            'branch_id' => $this->branch->id,
            'homework_id' => $homework->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        $response = $this->actingAs($this->teacher->user)->post(route('teacher.homeworks.submissions.grade', [$homework, $submission]), [
            'score' => 90
        ]);

        $response->assertForbidden();
    }

    public function test_student_submission()
    {
        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Assignment 1',
            'due_date' => now()->addDays(3),
            'status' => 'published'
        ]);

        $response = $this->actingAs($this->student->user)->post(route('student.homeworks.submit', $homework));
        $response->assertRedirect();
        
        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id' => $this->student->id,
            'status' => 'submitted'
        ]);
        
        Notification::assertSentTo($this->teacher->user, \App\Notifications\GeneralNotification::class);
    }

    public function test_late_submission()
    {
        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Assignment Late',
            'due_date' => now()->subDays(1),
            'allow_late_submission' => true,
            'status' => 'published'
        ]);

        $response = $this->actingAs($this->student->user)->post(route('student.homeworks.submit', $homework));
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id' => $this->student->id,
            'status' => 'late'
        ]);
    }

    public function test_subscription_limit()
    {
        $this->branch->subscription->plan->update(['limits' => ['max_homeworks' => 1]]);

        Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Math Assignment Existing',
            'due_date' => now()->addDays(2),
            'max_score' => 100,
            'status' => 'published'
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.homeworks.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Math Assignment 1',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'max_score' => 100,
            'allow_late_submission' => false,
            'status' => 'draft'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('homeworks', 1);
    }

    public function test_publish_homework()
    {
        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Assignment Publish',
            'due_date' => now()->addDays(3),
            'status' => 'draft'
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.homeworks.publish', $homework));
        $response->assertRedirect();
        
        $this->assertDatabaseHas('homeworks', [
            'id' => $homework->id,
            'status' => 'published'
        ]);
        
        Notification::assertSentTo([$this->student->user, $this->guardian->user], \App\Notifications\GeneralNotification::class);
    }

    public function test_grade_homework()
    {
        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Assignment Grade',
            'due_date' => now()->addDays(3),
            'max_score' => 100,
            'status' => 'published'
        ]);

        $submission = HomeworkSubmission::create([
            'branch_id' => $this->branch->id,
            'homework_id' => $homework->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        $response = $this->actingAs($this->teacher->user)->post(route('teacher.homeworks.submissions.grade', [$homework, $submission]), [
            'grade' => 85,
            'teacher_feedback' => 'Good job!'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('homework_submissions', [
            'id' => $submission->id,
            'grade' => 85,
            'status' => 'graded'
        ]);

        Notification::assertSentTo([$this->student->user, $this->guardian->user], \App\Notifications\GeneralNotification::class);
    }
}
