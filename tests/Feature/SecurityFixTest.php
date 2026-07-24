<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\ParentStudent;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\AttendanceSession;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Branch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SecurityFixTest extends TestCase
{
    use DatabaseTransactions;

    protected User $parentUser1;
    protected User $parentUser2;
    protected User $adminUser;
    protected Student $student1;
    protected Student $student2;

    protected User $teacherUser1;
    protected User $teacherUser2;
    protected Teacher $teacher1;
    protected Teacher $teacher2;
    protected Classroom $classroom1;
    protected Classroom $classroom2;
    protected Course $course;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);

        // 2. Branch
        $this->branch = Branch::firstOrFail();

        // 3. Classrooms & Course
        $this->classroom1 = Classroom::firstOrCreate(
            ['name' => 'Class A'],
            [
                'code' => 'CLA-A',
                'branch_id' => $this->branch->id,
                'capacity' => 20,
            ]
        );

        $this->classroom2 = Classroom::firstOrCreate(
            ['name' => 'Class B'],
            [
                'code' => 'CLA-B',
                'branch_id' => $this->branch->id,
                'capacity' => 20,
            ]
        );

        $this->course = Course::firstOrFail();

        // 4. Students
        $this->student1 = Student::create([
            'student_number' => 'STU001',
            'identity_number' => '11111111111',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom1->id,
            'status' => 'active'
        ]);

        $this->student2 = Student::create([
            'student_number' => 'STU002',
            'identity_number' => '22222222222',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom2->id,
            'status' => 'active'
        ]);

        // 5. Parents
        $this->parentUser1 = User::create([
            'name' => 'Parent One',
            'email' => 'parent1@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->parentUser1->roles()->attach($parentRole->id);

        $this->parentUser2 = User::create([
            'name' => 'Parent Two',
            'email' => 'parent2@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->parentUser2->roles()->attach($parentRole->id);

        // Link Parent 1 to Student 1
        ParentStudent::create([
            'parent_id' => $this->parentUser1->id,
            'student_id' => $this->student1->id,
            'relation_type' => 'Father'
        ]);

        // Link Parent 2 to Student 2
        ParentStudent::create([
            'parent_id' => $this->parentUser2->id,
            'student_id' => $this->student2->id,
            'relation_type' => 'Mother'
        ]);

        // 6. Admin
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->adminUser->roles()->attach($adminRole->id);

        // 7. Teachers
        $this->teacherUser1 = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher1@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->teacherUser1->roles()->attach($teacherRole->id);

        $this->teacher1 = Teacher::create([
            'user_id' => $this->teacherUser1->id,
            'branch_id' => $this->branch->id,
            'first_name' => 'Teacher',
            'last_name' => 'One',
            'email' => 'teacher1@test.com',
            'phone' => '1234567890',
            'status' => 'active'
        ]);

        $this->teacherUser2 = User::create([
            'name' => 'Teacher Two',
            'email' => 'teacher2@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->teacherUser2->roles()->attach($teacherRole->id);

        $this->teacher2 = Teacher::create([
            'user_id' => $this->teacherUser2->id,
            'branch_id' => $this->branch->id,
            'first_name' => 'Teacher',
            'last_name' => 'Two',
            'email' => 'teacher2@test.com',
            'phone' => '0987654321',
            'status' => 'active'
        ]);

        // Assign Teacher 1 to Classroom 1 and Course
        TeacherAssignment::create([
            'teacher_id' => $this->teacher1->id,
            'classroom_id' => $this->classroom1->id,
            'course_id' => $this->course->id,
            'status' => 'active'
        ]);

        // Assign Teacher 2 to Classroom 2 and Course
        TeacherAssignment::create([
            'teacher_id' => $this->teacher2->id,
            'classroom_id' => $this->classroom2->id,
            'course_id' => $this->course->id,
            'status' => 'active'
        ]);
    }

    public function test_parent_attempts_to_access_unlinked_student_id_is_forbidden(): void
    {
        $response = $this->actingAs($this->parentUser1)
            ->get(route('parent.dashboard', ['student_id' => $this->student2->id]));

        $response->assertStatus(403);
    }

    public function test_parent_accesses_own_linked_student_id_successfully(): void
    {
        $response = $this->actingAs($this->parentUser1)
            ->get(route('parent.dashboard', ['student_id' => $this->student1->id]));

        $response->assertOk();
    }

    public function test_teacher_attempts_to_access_unassigned_attendance_session_is_forbidden(): void
    {
        // Session created by Teacher 2 for Classroom 2
        $session = AttendanceSession::create([
            'teacher_id' => $this->teacher2->id,
            'classroom_id' => $this->classroom2->id,
            'course_id' => $this->course->id,
            'session_date' => today()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => 'active'
        ]);

        // Teacher 1 tries to access Teacher 2's session
        $response = $this->actingAs($this->teacherUser1)
            ->get(route('teacher.attendance', ['session_id' => $session->id]));

        $response->assertStatus(403);
    }

    public function test_teacher_accesses_own_attendance_session_successfully(): void
    {
        // Session created by Teacher 1 for Classroom 1
        $session = AttendanceSession::create([
            'teacher_id' => $this->teacher1->id,
            'classroom_id' => $this->classroom1->id,
            'course_id' => $this->course->id,
            'session_date' => today()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->teacherUser1)
            ->get(route('teacher.attendance', ['session_id' => $session->id]));

        $response->assertOk();
    }

    public function test_teacher_attempts_to_access_unassigned_assignment_is_forbidden(): void
    {
        // Assignment by Teacher 2
        $assignment = Assignment::create([
            'teacher_id' => $this->teacher2->id,
            'classroom_id' => $this->classroom2->id,
            'course_id' => $this->course->id,
            'title' => 'Math Assignment',
            'code' => 'MATH-HW-T2',
            'content' => 'Please solve questions.',
            'due_date' => today()->addDays(7)->toDateString(),
            'status' => 'Published'
        ]);

        $response = $this->actingAs($this->teacherUser1)
            ->get(route('teacher.homework', ['assignment_id' => $assignment->id]));

        $response->assertStatus(403);
    }

    public function test_teacher_accesses_own_assignment_successfully(): void
    {
        // Assignment by Teacher 1
        $assignment = Assignment::create([
            'teacher_id' => $this->teacher1->id,
            'classroom_id' => $this->classroom1->id,
            'course_id' => $this->course->id,
            'title' => 'Math Assignment',
            'code' => 'MATH-HW-T1',
            'content' => 'Please solve questions.',
            'due_date' => today()->addDays(7)->toDateString(),
            'status' => 'Published'
        ]);

        $response = $this->actingAs($this->teacherUser1)
            ->get(route('teacher.homework', ['assignment_id' => $assignment->id]));

        $response->assertOk();
    }

    public function test_administrator_can_bypass_parent_and_teacher_restrictions(): void
    {
        // Admin accessing parent dashboard with student 1
        $responseParent = $this->actingAs($this->adminUser)
            ->get(route('parent.dashboard', ['student_id' => $this->student1->id]));

        $responseParent->assertOk();

        // Admin accessing teacher dashboard
        $responseTeacher = $this->actingAs($this->adminUser)
            ->get(route('teacher.dashboard'));

        $responseTeacher->assertOk();
    }
}
