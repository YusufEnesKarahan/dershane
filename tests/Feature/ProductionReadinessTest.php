<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\PaymentPlan;
use App\Models\AcademicTerm;
use App\Models\Role;
use App\Core\Context\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $branch1;
    protected $branch2;
    protected $classroom;
    protected $academicTerm;
    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOnboardingCompleted::class,
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $rSuperAdmin = Role::where('name', 'Super Admin')->first();

        // Create branches
        $this->branch1 = Branch::create([
            'name' => 'Şube Adana',
            'slug' => 'sube-adana',
            'phone' => '5551112233',
            'email' => 'adana@test.com',
            'address' => 'Adana Merkez'
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Şube Mersin',
            'slug' => 'sube-mersin',
            'phone' => '5554445566',
            'email' => 'mersin@test.com',
            'address' => 'Mersin Merkez'
        ]);

        // Create Super Admin User
        $this->adminUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $this->adminUser->roles()->sync([$rSuperAdmin->id]);

        // Create Teacher model
        $this->teacher = Teacher::create([
            'branch_id' => $this->branch1->id,
            'user_id' => $this->adminUser->id,
            'status' => 'Active'
        ]);

        // Create Classroom
        $this->classroom = Classroom::create([
            'branch_id' => $this->branch1->id,
            'name' => '11-B',
            'code' => '11B',
            'capacity' => 35,
            'is_active' => true
        ]);

        // Create Academic Term
        $this->academicTerm = AcademicTerm::create([
            'branch_id' => $this->branch1->id,
            'name' => '2026-2027 Güz',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-31',
            'is_active' => true
        ]);
    }

    public function test_student_lifecycle_end_to_end()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->branch1->id]);

        // 1. Create Student
        $studentData = [
            'student_number' => 'STU-101',
            'identity_number' => '98765432101',
            'first_name' => 'Can',
            'last_name' => 'Yılmaz',
            'classroom_id' => $this->classroom->id
        ];

        $response = $this->post(route('admin.students.store'), $studentData);
        $response->assertRedirect();

        $student = Student::where('student_number', 'STU-101')->first();
        $this->assertNotNull($student);
        $this->assertEquals('11-B', $student->classroom->name);

        // 2. Soft Delete Student and verify payment plan relationship safety
        $plan = PaymentPlan::create([
            'branch_id' => $this->branch1->id,
            'student_id' => $student->id,
            'academic_term_id' => $this->academicTerm->id,
            'title' => 'Yıllık Eğitim Ücreti',
            'total_amount' => 50000,
            'discount_amount' => 5000,
            'net_amount' => 45000,
            'status' => 'active'
        ]);

        $student->delete();
        $this->assertTrue($student->trashed());

        // Verify relationship retrieves soft deleted student without crash
        $plan->refresh();
        $this->assertNotNull($plan->student);
        $this->assertEquals('Can Yılmaz', $plan->student->full_name);
    }

    public function test_teacher_lifecycle_end_to_end()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->branch1->id]);

        // 1. Create Teacher
        $teacherData = [
            'first_name' => 'Zeynep',
            'last_name' => 'Kaya',
            'email' => 'zeynep.kaya@test.com',
            'status' => 'Active'
        ];

        $response = $this->post(route('admin.teachers.store'), $teacherData);
        $response->assertRedirect();

        $teacherUser = User::where('email', 'zeynep.kaya@test.com')->first();
        $this->assertNotNull($teacherUser);
        $this->assertNotNull($teacherUser->teacher);

        // 2. Assign Teacher to Classroom
        $this->classroom->update(['teacher_id' => $teacherUser->teacher->id]);
        $this->assertEquals($teacherUser->teacher->id, $this->classroom->fresh()->teacher_id);
    }

    public function test_exam_lifecycle_and_result_ranking()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->branch1->id]);

        $exam = Exam::create([
            'branch_id' => $this->branch1->id,
            'title' => 'Matematik Tarama 1',
            'type' => 'quiz',
            'exam_date' => now()->toDateString(),
            'total_score' => 100,
            'status' => 'published',
            'created_by' => $this->adminUser->id
        ]);

        $student = Student::create([
            'branch_id' => $this->branch1->id,
            'student_number' => 'STU-102',
            'first_name' => 'Elif',
            'last_name' => 'Şahin'
        ]);

        // Store result
        $resultService = app(\App\Domain\Exam\Services\ExamResultService::class);
        $result = $resultService->submitResult($exam, [
            'student_id' => $student->id,
            'score' => 85,
            'correct_answers' => 20,
            'wrong_answers' => 4,
            'empty_answers' => 1
        ]);

        $result->refresh();

        $this->assertEquals(85, $result->score);
        $this->assertEquals(1, $result->rank);
    }

    public function test_attendance_duplicate_prevention()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->branch1->id]);

        $student = Student::create([
            'branch_id' => $this->branch1->id,
            'student_number' => 'STU-103',
            'first_name' => 'Burak',
            'last_name' => 'Erden'
        ]);

        $session = AttendanceSession::create([
            'branch_id' => $this->branch1->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'session_date' => now()->toDateString(),
            'status' => 'open'
        ]);

        $attendanceService = app(\App\Domain\Attendance\Services\AttendanceManagementService::class);

        // First mark
        $attendanceService->markStudentAttendance([
            'branch_id' => $this->branch1->id,
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'present',
            'created_by' => $this->adminUser->id
        ]);

        // Duplicate mark on same session/student
        $attendanceService->markStudentAttendance([
            'branch_id' => $this->branch1->id,
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'absent',
            'created_by' => $this->adminUser->id
        ]);

        // Verify only 1 record exists and status was updated
        $recordsCount = AttendanceRecord::where('student_id', $student->id)
            ->where('attendance_session_id', $session->id)
            ->count();

        $this->assertEquals(1, $recordsCount);
        $this->assertEquals('absent', AttendanceRecord::where('student_id', $student->id)->first()->status);
    }

    public function test_strict_tenant_isolation_between_branches()
    {
        // 1. Create Student in Branch 1
        $studentBranch1 = Student::create([
            'branch_id' => $this->branch1->id,
            'student_number' => 'STU-B1',
            'first_name' => 'Selin',
            'last_name' => 'Aksoy'
        ]);

        // 2. Create Student in Branch 2
        $studentBranch2 = Student::create([
            'branch_id' => $this->branch2->id,
            'student_number' => 'STU-B2',
            'first_name' => 'Deniz',
            'last_name' => 'Vural'
        ]);

        // 3. Set Active Branch to Branch 1
        TenantContext::setActiveBranchId($this->branch1->id);

        $studentsBranch1Query = Student::all();
        $this->assertTrue($studentsBranch1Query->contains('id', $studentBranch1->id));
        $this->assertFalse($studentsBranch1Query->contains('id', $studentBranch2->id));

        // 4. Set Active Branch to Branch 2
        TenantContext::setActiveBranchId($this->branch2->id);

        $studentsBranch2Query = Student::all();
        $this->assertFalse($studentsBranch2Query->contains('id', $studentBranch1->id));
        $this->assertTrue($studentsBranch2Query->contains('id', $studentBranch2->id));

        TenantContext::clear();
    }
}
