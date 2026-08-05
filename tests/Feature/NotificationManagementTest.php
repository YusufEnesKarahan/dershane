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
use App\Models\Homework;
use App\Models\Exam;
use App\Models\AttendanceSession;
use App\Models\Notification;
use App\Domain\Homework\Services\HomeworkManagementService;
use App\Domain\Attendance\Services\AttendanceManagementService;
use App\Domain\Exam\Services\ExamResultService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationManagementTest extends TestCase
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

        // Teacher
        $teacherUser = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->teacher = Teacher::create(['user_id' => $teacherUser->id, 'branch_id' => $this->branch->id]);
        $teacherRole = Role::create(['name' => 'Teacher']);
        $pNotifSend = Permission::firstOrCreate(['name' => 'notification.send']);
        $pNotifView = Permission::firstOrCreate(['name' => 'notification.view']);
        $teacherRole->permissions()->attach([$pNotifSend->id, $pNotifView->id]);
        $teacherUser->roles()->attach($teacherRole);

        // Student
        $studentUser = User::create([
            'name' => 'Student One',
            'email' => 'student@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'first_name' => 'Student',
            'last_name' => 'One',
            'student_number' => 'STU900'
        ]);
        $studentRole = Role::create(['name' => 'Student']);
        $pStudentView = Permission::firstOrCreate(['name' => 'student.view_profile']);
        $studentRole->permissions()->attach([$pNotifView->id, $pStudentView->id]);
        $studentUser->roles()->attach($studentRole);

        // Parent
        $parentUser = User::create([
            'name' => 'Parent One',
            'email' => 'parent@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $this->guardian = StudentGuardian::create([
            'user_id' => $parentUser->id,
            'student_id' => $this->student->id,
            'guardian_name' => 'Parent One',
            'relation' => 'Mother',
            'phone' => '5559876543'
        ]);
        $parentRole = Role::create(['name' => 'Parent']);
        $parentRole->permissions()->attach([$pNotifView->id]);
        $parentUser->roles()->attach($parentRole);

        // Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        $adminRole = Role::create(['name' => 'Super Admin']);
        $pNotifManage = Permission::firstOrCreate(['name' => 'notification.manage']);
        $adminRole->permissions()->attach([$pNotifManage->id, $pNotifView->id, $pNotifSend->id]);
        $this->admin->roles()->attach($adminRole);
    }

    public function test_admin_can_create_notification()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.notifications.store'), [
            'receiver_id' => $this->student->user_id,
            'receiver_type' => 'student',
            'title' => 'Admin Test Notice',
            'message' => 'Hello student from admin',
            'type' => 'announcement'
        ]);

        $response->assertRedirect(route('admin.notifications.index'));
        $this->assertDatabaseHas('notifications', [
            'receiver_id' => $this->student->user_id,
            'title' => 'Admin Test Notice',
            'type' => 'announcement'
        ]);
    }

    public function test_teacher_can_send_notification_to_student()
    {
        $response = $this->actingAs($this->teacher->user)->post(route('teacher.notifications.store'), [
            'student_id' => $this->student->id,
            'title' => 'Homework Reminder',
            'message' => 'Please do your math homework',
            'type' => 'homework'
        ]);

        $response->assertRedirect(route('teacher.notifications.index'));
        $this->assertDatabaseHas('notifications', [
            'receiver_id' => $this->student->user_id,
            'title' => 'Homework Reminder'
        ]);
    }

    public function test_student_can_view_own_notifications()
    {
        Notification::create([
            'branch_id' => $this->branch->id,
            'receiver_id' => $this->student->user_id,
            'user_id' => $this->student->user_id,
            'receiver_type' => 'student',
            'title' => 'Personal Announcement',
            'message' => 'Your class schedule updated',
            'type' => 'announcement'
        ]);

        $response = $this->actingAs($this->student->user)->get(route('student.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Personal Announcement');
    }

    public function test_parent_can_view_notifications()
    {
        Notification::create([
            'branch_id' => $this->branch->id,
            'receiver_id' => $this->guardian->user_id,
            'user_id' => $this->guardian->user_id,
            'receiver_type' => 'parent',
            'title' => 'Parent Attendance Alert',
            'message' => 'Child was absent today',
            'type' => 'attendance'
        ]);

        $response = $this->actingAs($this->guardian->user)->get(route('parent.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Parent Attendance Alert');
    }

    public function test_tenant_isolation()
    {
        $otherBranch = Branch::create(['name' => 'Other Branch', 'slug' => 'other-branch']);
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $otherBranch->id
        ]);

        Notification::create([
            'branch_id' => $otherBranch->id,
            'receiver_id' => $otherUser->id,
            'user_id' => $otherUser->id,
            'receiver_type' => 'student',
            'title' => 'Secret Other Branch Notice',
            'message' => 'Do not reveal to main branch',
            'type' => 'system'
        ]);

        $response = $this->actingAs($this->student->user)->get(route('student.notifications.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Secret Other Branch Notice');
    }

    public function test_mark_as_read()
    {
        $notif = Notification::create([
            'branch_id' => $this->branch->id,
            'receiver_id' => $this->student->user_id,
            'user_id' => $this->student->user_id,
            'receiver_type' => 'student',
            'title' => 'Unread Notice',
            'message' => 'Read me please',
            'type' => 'system'
        ]);

        $response = $this->actingAs($this->student->user)->post(route('student.notifications.read', $notif));
        $response->assertRedirect();
        
        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_module_event_integrations()
    {
        // 1. Homework publish integration
        $homework = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Calculus Task 1',
            'due_date' => now()->addDays(5),
            'max_score' => 100,
            'status' => 'draft'
        ]);

        app(HomeworkManagementService::class)->publishHomework($homework);

        $this->assertDatabaseHas('notifications', [
            'receiver_id' => $this->student->user_id,
            'type' => 'system'
        ]);

        // 2. Attendance absent integration
        $attendanceService = app(AttendanceManagementService::class);
        $session = $attendanceService->createSession([
            'branch_id' => $this->branch->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString()
        ]);

        $attendanceService->bulkMarkAttendance($session, [
            [
                'student_id' => $this->student->id,
                'status' => 'absent'
            ]
        ], $this->teacher->user_id);

        $this->assertDatabaseHas('notifications', [
            'receiver_id' => $this->guardian->user_id,
            'type' => 'attendance'
        ]);

        // 3. Exam result integration
        $exam = Exam::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'title' => 'Midterm Exam 1',
            'exam_date' => now()->toDateString(),
            'total_max_score' => 100,
            'status' => 'published',
            'created_by' => $this->admin->id
        ]);

        app(ExamResultService::class)->submitResult($exam, [
            'student_id' => $this->student->id,
            'score' => 85,
            'correct_answers' => 17,
            'wrong_answers' => 3,
            'empty_answers' => 0
        ]);

        $this->assertDatabaseHas('notifications', [
            'receiver_id' => $this->student->user_id,
            'type' => 'exam'
        ]);
    }

    public function test_admin_can_view_notification_dashboard()
    {
        Notification::create([
            'branch_id' => $this->branch->id,
            'receiver_id' => $this->student->user_id,
            'user_id' => $this->student->user_id,
            'receiver_type' => 'student',
            'title' => 'Dashboard Test Notice',
            'message' => 'Hello',
            'type' => 'system',
            'status' => 'Read',
            'read_at' => now(),
            'sent_at' => now(),
            'channel' => 'sms',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.notifications.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Toplam bildirim');
    }

    public function test_admin_can_manage_preferences()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.notifications.preferences'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->put(route('admin.notifications.preferences.update'), [
            'panel_enabled' => '1',
            'email_enabled' => '1',
        ]);
        $response->assertRedirect();
        
        $userPreferences = $this->admin->fresh()->preferences;
        $this->assertTrue($userPreferences['panel_enabled']);
        $this->assertTrue($userPreferences['email_enabled']);
        $this->assertFalse($userPreferences['sms_enabled']);
    }
}
