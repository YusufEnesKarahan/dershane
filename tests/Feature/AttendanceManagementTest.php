<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Plan;
use App\Models\Subscription;

class AttendanceManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckOnboardingStatus::class,
            \App\Http\Middleware\CheckRole::class
        ]);
        \Illuminate\Support\Facades\Cache::flush();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->branch = Branch::factory()->create(['name' => 'Main Branch', 'slug' => 'main-branch']);
        
        // Setup Plan & Subscription for Limits
        $this->plan = Plan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'price' => 100,
            'max_students' => 100,
            'max_teachers' => 10,
            'max_classrooms' => 10,
            'max_exams' => 10,
            'limits' => [
                'max_daily_attendance' => 5
            ]
        ]);
        $license = \App\Models\License::create([
            'license_key' => 'TEST-KEY-12345',
            'status' => 'active',
            'type' => 'saas',
            'expires_at' => now()->addYear()
        ]);
        
        $this->subscription = Subscription::create([
            'branch_id' => $this->branch->id,
            'plan_id' => $this->plan->id,
            'license_id' => $license->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYear()
        ]);

        $this->admin = User::factory()->create(['branch_id' => $this->branch->id]);
        $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
        $this->admin->roles()->attach($superAdminRole);

        $this->teacherUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $teacherRole = \App\Models\Role::where('name', 'Teacher')->first();
        $this->teacherUser->roles()->attach($teacherRole);
        $this->teacher = Teacher::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->teacherUser->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'employee_id' => 'EMP123',
            'status' => 'active'
        ]);

        $this->studentUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $studentRole = \App\Models\Role::where('name', 'Student')->first();
        $this->studentUser->roles()->attach($studentRole);
        $this->student = Student::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->studentUser->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'student_number' => 'STU123',
            'status' => 'active'
        ]);

        $this->classroom = Classroom::create([
            'branch_id' => $this->branch->id,
            'teacher_id' => $this->teacher->id,
            'name' => 'Class 101',
            'code' => 'C101',
            'capacity' => 30
        ]);
        $this->classroom->students()->attach($this->student->id);
    }

    public function test_admin_can_create_attendance_session()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_sessions', [
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id
        ]);
    }

    public function test_teacher_can_take_attendance_for_own_class()
    {
        $session = AttendanceSession::create([
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);

        $response = $this->actingAs($this->teacherUser)->put(route('teacher.attendance.update', $session), [
            'records' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'present',
                    'note' => 'On time'
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendance_records', [
            'branch_id' => $this->branch->id,
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'status' => 'present'
        ]);
    }

    public function test_teacher_cannot_take_attendance_for_others_class()
    {
        $otherTeacherUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $otherTeacher = Teacher::create([
            'branch_id' => $this->branch->id,
            'user_id' => $otherTeacherUser->id,
            'first_name' => 'Other',
            'last_name' => 'Teacher',
            'employee_id' => 'EMP999',
            'status' => 'active'
        ]);
        $session = AttendanceSession::create([
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $otherTeacher->id,
            'session_date' => now()->toDateString()
        ]);

        $response = $this->actingAs($this->teacherUser)->put(route('teacher.attendance.update', $session), [
            'records' => [
                [
                    'student_id' => $this->student->id,
                    'status' => 'present'
                ]
            ]
        ]);

        $response->assertStatus(403);
    }

    public function test_student_can_only_view_own_attendance()
    {
        $session = AttendanceSession::create([
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);
        
        AttendanceRecord::create([
            'branch_id' => $this->branch->id,
            'attendance_session_id' => $session->id,
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'late'
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('student.attendance.index'));
        $response->assertStatus(200);
        $response->assertViewHas('summary');
        
        $summary = $response->original->getData()['summary'];
        $this->assertEquals(1, $summary['total']);
        $this->assertEquals(0, $summary['absence_rate']); // Since late doesn't count as absent in my basic logic
    }

    public function test_tenant_isolation_is_enforced()
    {
        $otherBranch = Branch::factory()->create(['name' => 'Other Branch', 'slug' => 'other-branch']);
        $otherAdmin = User::factory()->create(['branch_id' => $otherBranch->id]);
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        $otherAdmin->roles()->attach($adminRole);
        
        $sessionModel = AttendanceSession::create([
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);

        // Attempt to show attendance from another branch
        $response = $this->withSession(['active_branch_id' => $otherBranch->id])
            ->actingAs($otherAdmin)
            ->get(route('admin.attendance.show', $sessionModel->id));
            
        // Should be 404 (TenantScoped) or 403 (Policy)
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_duplicate_attendance_is_prevented()
    {
        $session = AttendanceSession::create([
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);
        
        $response1 = $this->actingAs($this->teacherUser)->put(route('teacher.attendance.update', $session), [
            'records' => [
                ['student_id' => $this->student->id, 'status' => 'absent']
            ]
        ]);
        
        // Second time, same student, same session
        $response2 = $this->actingAs($this->teacherUser)->put(route('teacher.attendance.update', $session), [
            'records' => [
                ['student_id' => $this->student->id, 'status' => 'present']
            ]
        ]);

        $this->assertEquals(1, AttendanceRecord::count());
        $this->assertEquals('present', AttendanceRecord::first()->status);
    }

    public function test_subscription_limit_enforced()
    {
        // Limit is 5
        for ($i = 0; $i < 5; $i++) {
            AttendanceSession::create([
                'branch_id' => $this->branch->id,
                'classroom_id' => $this->classroom->id,
                'teacher_id' => $this->teacher->id,
                'session_date' => now()->toDateString()
            ]);
        }

        $response = $this->actingAs($this->admin)->post(route('admin.attendance.store'), [
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);

        // It should throw an exception or return a 500/302 (redirect back with error)
        $this->assertTrue(in_array($response->status(), [302, 500, 403]));
        
        $this->assertEquals(5, AttendanceSession::count());
    }
}
