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
use App\Models\LessonSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
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

        $this->branch = Branch::create(['name' => 'Main Branch', 'slug' => 'main-branch']);
        $this->term = AcademicTerm::create(['branch_id' => $this->branch->id, 'name' => 'Fall 2026', 'start_date' => '2026-09-01', 'end_date' => '2027-01-31']);
        $this->classroom = Classroom::create(['branch_id' => $this->branch->id, 'name' => '12-A', 'code' => '12A', 'capacity' => 20]);
        $this->course = Course::create(['branch_id' => $this->branch->id, 'name' => 'Mathematics', 'code' => 'MATH101', 'slug' => 'mathematics-math101']);

        // Teacher Setup
        $teacherUser = User::create([
            'name' => 'Math Teacher',
            'email' => 'teacher@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $this->branch->id]);
        $teacherRole = Role::create(['name' => 'Teacher']);
        $pScheduleView = Permission::firstOrCreate(['name' => 'schedule.view']);
        $teacherRole->permissions()->attach([$pScheduleView->id]);
        $teacherUser->roles()->attach($teacherRole);

        // Student Setup
        $studentUser = User::create([
            'name' => 'Ali Student',
            'email' => 'student@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $studentRole = Role::create(['name' => 'Student']);
        $pStudentProfile = Permission::firstOrCreate(['name' => 'student.view_profile']);
        $studentRole->permissions()->attach([$pStudentProfile->id, $pScheduleView->id]);
        $studentUser->roles()->attach($studentRole);
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'first_name' => 'Ali',
            'last_name' => 'Yilmaz',
            'student_number' => 'STU1001'
        ]);

        // Parent Setup
        $parentUser = User::create([
            'name' => 'Veli Yilmaz',
            'email' => 'parent@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $parentRole = Role::create(['name' => 'Parent']);
        $parentRole->permissions()->attach([$pScheduleView->id]);
        $parentUser->roles()->attach($parentRole);
        $this->guardian = StudentGuardian::create([
            'user_id' => $parentUser->id,
            'student_id' => $this->student->id,
            'guardian_name' => 'Veli Yilmaz',
            'relation' => 'Father',
            'phone' => '5551234567'
        ]);

        // Admin Setup
        $this->admin = User::create([
            'name' => 'Branch Admin',
            'email' => 'admin@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $adminRole = Role::create(['name' => 'Super Admin']);
        $pScheduleCreate = Permission::firstOrCreate(['name' => 'schedule.create']);
        $pScheduleUpdate = Permission::firstOrCreate(['name' => 'schedule.update']);
        $pScheduleDelete = Permission::firstOrCreate(['name' => 'schedule.delete']);
        $adminRole->permissions()->attach([$pScheduleView->id, $pScheduleCreate->id, $pScheduleUpdate->id, $pScheduleDelete->id]);
        $this->admin->roles()->attach($adminRole);
    }

    public function test_admin_can_create_schedule()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.schedule.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '09:40',
            'room' => '101'
        ]);

        $response->assertRedirect(route('admin.schedule.index'));
        $this->assertDatabaseHas('lesson_schedules', [
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday'
        ]);
    }

    public function test_teacher_can_view_own_schedule()
    {
        $schedule = LessonSchedule::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '09:40'
        ]);

        $response = $this->actingAs($this->teacher->user)->get(route('teacher.schedule.index'));
        $response->assertStatus(200);
        $response->assertSee('Mathematics');
    }

    public function test_student_can_view_classroom_schedule()
    {
        $schedule = LessonSchedule::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Tuesday',
            'start_time' => '10:00',
            'end_time' => '10:40'
        ]);

        $response = $this->actingAs($this->student->user)->get(route('student.schedule.index'));
        $response->assertStatus(200);
        $response->assertSee('Tuesday');
    }

    public function test_parent_can_view_child_schedule()
    {
        $schedule = LessonSchedule::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Wednesday',
            'start_time' => '11:00',
            'end_time' => '11:40'
        ]);

        $response = $this->actingAs($this->guardian->user)->get(route('parent.schedule.index'));
        $response->assertStatus(200);
        $response->assertSee('Wednesday');
    }

    public function test_teacher_conflict_prevention()
    {
        LessonSchedule::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '09:40'
        ]);

        $classroom2 = Classroom::create(['branch_id' => $this->branch->id, 'name' => '12-B', 'code' => '12B', 'capacity' => 20]);

        $response = $this->actingAs($this->admin)->post(route('admin.schedule.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $classroom2->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:20', // Overlaps with 09:00-09:40
            'end_time' => '10:00'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('lesson_schedules', 1);
    }

    public function test_classroom_conflict_prevention()
    {
        LessonSchedule::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00',
            'end_time' => '09:40'
        ]);

        $teacherUser2 = User::create([
            'name' => 'Physics Teacher',
            'email' => 'physics@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'branch_id' => $this->branch->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.schedule.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $teacher2->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:30', // Overlaps with 09:00-09:40
            'end_time' => '10:10'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('lesson_schedules', 1);
    }

    public function test_tenant_isolation()
    {
        $otherBranch = Branch::create(['name' => 'Other Branch', 'slug' => 'other-branch']);
        $otherTerm = AcademicTerm::create(['branch_id' => $otherBranch->id, 'name' => 'Fall 2026', 'start_date' => '2026-09-01', 'end_date' => '2027-01-31']);
        $otherClassroom = Classroom::create(['branch_id' => $otherBranch->id, 'name' => '10-A', 'code' => '10A', 'capacity' => 20]);
        $otherCourse = Course::create(['branch_id' => $otherBranch->id, 'name' => 'Chemistry', 'code' => 'CHEM101', 'slug' => 'chemistry-chem101']);
        $otherTeacherUser = User::create([
            'name' => 'Chem Teacher',
            'email' => 'chem@other.com',
            'password' => bcrypt('password'),
            'branch_id' => $otherBranch->id
        ]);
        $otherTeacher = Teacher::create(['user_id' => $otherTeacherUser->id, 'branch_id' => $otherBranch->id]);

        $otherSchedule = LessonSchedule::create([
            'branch_id' => $otherBranch->id,
            'academic_term_id' => $otherTerm->id,
            'classroom_id' => $otherClassroom->id,
            'course_id' => $otherCourse->id,
            'teacher_id' => $otherTeacher->id,
            'day_of_week' => 'Friday',
            'start_time' => '14:00',
            'end_time' => '14:40'
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.schedule.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Chemistry');
    }
}
