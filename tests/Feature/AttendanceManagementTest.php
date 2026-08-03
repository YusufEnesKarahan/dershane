<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\AttendanceSession;
use App\Models\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Role;
use App\Models\Permission;

class AttendanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([\App\Http\Middleware\CheckOnboardingStatus::class]);
        


        // Create Permissions
        Permission::create(['name' => 'attendance.view']);
        Permission::create(['name' => 'attendance.create']);
        Permission::create(['name' => 'attendance.update']);
        Permission::create(['name' => 'attendance.report']);
        
        // Create Statuses
        AttendanceStatus::create(['name' => 'Burada', 'code' => 'P']);
        AttendanceStatus::create(['name' => 'Yok', 'code' => 'A']);
        AttendanceStatus::create(['name' => 'Geç Kaldı', 'code' => 'L']);
    }

    public function test_tenant_admin_can_create_and_manage_attendance_sessions()
    {
        $branch = Branch::factory()->create(['name' => 'Branch 1', 'slug' => 'branch-1']);
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $role = Role::create(['name' => 'Tenant Admin']);
        $permissions = Permission::whereIn('name', ['attendance.view', 'attendance.create', 'attendance.update', 'attendance.report'])->pluck('id');
        $role->permissions()->syncWithoutDetaching($permissions);
        $admin->roles()->attach($role->id);

        $classroom = Classroom::create(['name' => '10-A', 'code' => '10A', 'branch_id' => $branch->id, 'capacity' => 30]);
        $course = Course::create(['name' => 'Math', 'code' => 'MTH', 'slug' => 'math', 'branch_id' => $branch->id]);
        $teacherUser = User::factory()->create(['branch_id' => $branch->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $branch->id]);

        session(['active_branch_id' => $branch->id]);
        
        $this->withoutExceptionHandling();
        $response = $this->actingAs($admin)->post(route('admin.attendance.store'), [
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_sessions', [
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'branch_id' => $branch->id,
        ]);

        $session = AttendanceSession::first();

        $studentUser = User::factory()->create(['branch_id' => $branch->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'branch_id' => $branch->id, 'student_number' => '1001', 'first_name' => 'Test', 'last_name' => 'Student']);
        $student->classrooms()->attach($classroom->id);

        $responseTake = $this->actingAs($admin)->post(route('admin.attendance.storeBulk', $session->id), [
            'attendances' => [
                $student->id => 'P'
            ]
        ]);

        $responseTake->assertRedirect(route('admin.attendance.index'));
        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_teacher_can_take_attendance_in_own_class()
    {
        $branch = Branch::factory()->create(['name' => 'Branch 1', 'slug' => 'branch-1']);
        $teacherUser = User::factory()->create(['branch_id' => $branch->id]);
        $role = Role::create(['name' => 'Teacher']);
        $permissions = Permission::whereIn('name', ['attendance.view', 'attendance.update'])->pluck('id');
        $role->permissions()->syncWithoutDetaching($permissions);
        $teacherUser->roles()->attach($role->id);
        
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $branch->id]);

        $classroom = Classroom::create(['name' => '10-A', 'code' => '10A', 'branch_id' => $branch->id, 'teacher_id' => $teacher->id, 'capacity' => 30]);
        $course = Course::create(['name' => 'Math', 'code' => 'MTH', 'slug' => 'math', 'branch_id' => $branch->id]);
        
        \App\Models\TeacherAssignment::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'branch_id' => $branch->id,
            'is_primary' => true,
        ]);
        
        $session = AttendanceSession::create([
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'branch_id' => $branch->id,
            'session_date' => now(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $studentUser = User::factory()->create(['branch_id' => $branch->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'branch_id' => $branch->id, 'student_number' => '1001', 'first_name' => 'Test', 'last_name' => 'Student']);
        $student->classrooms()->attach($classroom->id);

        session(['active_branch_id' => $branch->id]);
        $response = $this->actingAs($teacherUser)->post(route('teacher.attendance.store'), [
            'session_id' => $session->id,
            'records' => [
                $student->id => 'A' // Absent
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
        ]);
        
        $attendance = Attendance::first();
        $this->assertEquals('A', $attendance->status->code);
    }

    public function test_teacher_is_blocked_from_taking_attendance_in_other_class()
    {
        $branch = Branch::factory()->create(['name' => 'Branch 1', 'slug' => 'branch-1']);
        
        // Teacher 1
        $teacherUser1 = User::factory()->create(['branch_id' => $branch->id]);
        $role = Role::create(['name' => 'Teacher']);
        $permissions = Permission::whereIn('name', ['attendance.view', 'attendance.update'])->pluck('id');
        $role->permissions()->syncWithoutDetaching($permissions);
        $teacherUser1->roles()->attach($role->id);
        $teacher1 = Teacher::create(['user_id' => $teacherUser1->id, 'branch_id' => $branch->id]);

        // Teacher 2
        $teacherUser2 = User::factory()->create(['branch_id' => $branch->id]);
        $teacherUser2->roles()->attach($role->id);
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'branch_id' => $branch->id]);

        // Classroom belongs to Teacher 2
        $classroom = Classroom::create(['name' => '10-A', 'code' => '10A', 'branch_id' => $branch->id, 'teacher_id' => $teacher2->id, 'capacity' => 30]);
        $course = Course::create(['name' => 'Math', 'code' => 'MTH', 'slug' => 'math', 'branch_id' => $branch->id]);
        
        $session = AttendanceSession::create([
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher2->id,
            'branch_id' => $branch->id,
            'session_date' => now(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $studentUser = User::factory()->create(['branch_id' => $branch->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'branch_id' => $branch->id, 'student_number' => '1001', 'first_name' => 'Test', 'last_name' => 'Student']);
        $student->classrooms()->attach($classroom->id);

        session(['active_branch_id' => $branch->id]);
        // Teacher 1 tries to take attendance for Teacher 2's session
        $response = $this->actingAs($teacherUser1)->post(route('teacher.attendance.store'), [
            'session_id' => $session->id,
            'records' => [
                $student->id => 'P'
            ]
        ]);

        $response->assertStatus(403);
    }

    public function test_tenant_isolation_on_attendance()
    {
        // Branch 1
        $branch1 = Branch::factory()->create(['name' => 'Branch 1', 'slug' => 'branch-1']);
        $admin1 = User::factory()->create(['branch_id' => $branch1->id]);
        $role = Role::create(['name' => 'Tenant Admin']);
        $permissions = Permission::whereIn('name', ['attendance.view', 'attendance.create', 'attendance.update', 'attendance.report'])->pluck('id');
        $role->permissions()->syncWithoutDetaching($permissions);
        $admin1->roles()->attach($role->id);

        // Branch 2
        $branch2 = Branch::factory()->create(['name' => 'Branch 2', 'slug' => 'branch-2']);
        $classroom2 = Classroom::create(['name' => '10-B', 'code' => '10B', 'branch_id' => $branch2->id, 'capacity' => 30]);
        $course2 = Course::create(['name' => 'Math 2', 'code' => 'MTH2', 'slug' => 'math-2', 'branch_id' => $branch2->id]);
        $teacherUser2 = User::factory()->create(['branch_id' => $branch2->id]);
        $teacher2 = Teacher::create(['user_id' => $teacherUser2->id, 'branch_id' => $branch2->id]);
        
        $session2 = AttendanceSession::create([
            'classroom_id' => $classroom2->id,
            'course_id' => $course2->id,
            'teacher_id' => $teacher2->id,
            'branch_id' => $branch2->id,
            'session_date' => now(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        // Admin 1 tries to view Branch 2's session
        session(['active_branch_id' => $branch1->id]);
        $response = $this->actingAs($admin1)->get(route('admin.attendance.take', $session2->id));
        $response->assertStatus(403);
    }

    public function test_student_attendance_security()
    {
        $branch = Branch::factory()->create(['name' => 'Branch 1', 'slug' => 'branch-1']);
        $admin = User::factory()->create(['branch_id' => $branch->id]);
        $role = Role::create(['name' => 'Tenant Admin']);
        $permissions = Permission::whereIn('name', ['attendance.view', 'attendance.create', 'attendance.update', 'attendance.report'])->pluck('id');
        $role->permissions()->syncWithoutDetaching($permissions);
        $admin->roles()->attach($role->id);

        $classroom = Classroom::create(['name' => '10-A', 'code' => '10A', 'branch_id' => $branch->id, 'capacity' => 30]);
        $course = Course::create(['name' => 'Math', 'code' => 'MTH', 'slug' => 'math', 'branch_id' => $branch->id]);
        $teacherUser = User::factory()->create(['branch_id' => $branch->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $branch->id]);

        $session = AttendanceSession::create([
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'branch_id' => $branch->id,
            'session_date' => now(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        // Student NOT in classroom
        $studentUser = User::factory()->create(['branch_id' => $branch->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'branch_id' => $branch->id, 'student_number' => '1001', 'first_name' => 'Test', 'last_name' => 'Student']);
        // Do NOT attach to classroom

        session(['active_branch_id' => $branch->id]);
        $response = $this->actingAs($admin)->post(route('admin.attendance.storeBulk', $session->id), [
            'attendances' => [
                $student->id => 'P'
            ]
        ]);

        // Should be forbidden because student is not in this classroom
        $response->assertStatus(403);
    }
}
