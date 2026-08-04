<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\AcademicTerm;
use App\Models\StudentGuidanceRecord;
use App\Domain\Guidance\Services\StudentPerformanceService;

class GuidanceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $branch;
    protected $term;
    protected $otherBranchAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->withoutMiddleware([
            \App\Http\Middleware\CheckOnboardingStatus::class,
            \App\Http\Middleware\CheckRole::class
        ]);
        \Illuminate\Support\Facades\Cache::flush();

        $this->branch = Branch::factory()->create(['name' => 'Main Branch', 'slug' => 'main-branch']);
        $this->term = AcademicTerm::create(['branch_id' => $this->branch->id, 'name' => 'Fall 2026', 'start_date' => now(), 'end_date' => now()->addMonths(6)]);

        $this->admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'branch_id' => $this->branch->id
        ]);
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        $this->admin->roles()->attach($adminRole);
        
        $pCreate = \App\Models\Permission::firstOrCreate(['name' => 'guidance.create']);
        $pView = \App\Models\Permission::firstOrCreate(['name' => 'guidance.view']);
        $adminRole->permissions()->attach([$pCreate->id, $pView->id]);

        $teacherUser = User::create([
            'name' => 'Teacher', 'email' => 'teacher@test.com', 'password' => bcrypt('password'), 'branch_id' => $this->branch->id
        ]);
        $this->teacher = Teacher::create([
            'user_id' => $teacherUser->id, 'branch_id' => $this->branch->id, 'first_name' => 'T', 'last_name' => 'T'
        ]);
        
        $studentUser = User::create([
            'name' => 'Student', 'email' => 'student@test.com', 'password' => bcrypt('password'), 'branch_id' => $this->branch->id
        ]);
        $this->student = Student::create([
            'user_id' => $studentUser->id, 'branch_id' => $this->branch->id, 'first_name' => 'S', 'last_name' => 'S', 'student_number' => '123'
        ]);

        $otherBranch = Branch::create(['name' => 'Other Branch', 'slug' => 'other-branch']);
        $this->otherBranchAdmin = User::create([
            'name' => 'Other', 'email' => 'other@test.com', 'password' => bcrypt('password'), 'branch_id' => $otherBranch->id
        ]);
        $this->otherBranchAdmin->roles()->attach($adminRole);
    }

    public function test_admin_can_create_guidance_record()
    {
        $superAdminRole = \App\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $this->admin->roles()->attach($superAdminRole);
        
        $response = $this->actingAs($this->admin)->post(route('admin.guidance.store'), [
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'academic_term_id' => $this->term->id,
            'category' => 'Academic',
            'title' => 'Low Grades',
            'priority' => 'Medium',
        ]);

        $response->assertRedirect(route('admin.guidance.index'));
        $this->assertDatabaseHas('student_guidance_records', [
            'title' => 'Low Grades',
            'branch_id' => $this->branch->id
        ]);
    }

    public function test_tenant_isolation_in_guidance()
    {
        $record = StudentGuidanceRecord::create([
            'branch_id' => $this->branch->id,
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'academic_term_id' => $this->term->id,
            'category' => 'Behavior',
            'title' => 'Skipping Classes',
            'priority' => 'High',
            'status' => 'Open'
        ]);

        $response = $this->actingAs($this->otherBranchAdmin)->get(route('admin.guidance.show', $record->id));
        $response->assertStatus(403);
    }

    public function test_performance_engine_calculates_risk_score()
    {
        $service = app(StudentPerformanceService::class);
        $snapshot = $service->generateSnapshot($this->student->id, $this->term->id);

        $this->assertNotNull($snapshot);
        $this->assertEquals($this->branch->id, $snapshot->branch_id);
        // By default no attendance/exam data -> attendance 100%, exams 0%, hw 100%, late 0%
        // Risk = exams < 50 (+3 points) => 3 points => 'Medium' risk
        $this->assertEquals('Medium', $snapshot->risk_score);

        $this->assertDatabaseHas('student_risk_levels', [
            'student_id' => $this->student->id,
            'level' => 'Medium'
        ]);
    }

    public function test_teacher_can_view_own_students_performance()
    {
        $teacherRole = \App\Models\Role::firstOrCreate(['name' => 'Teacher']);
        $this->teacher->user->roles()->attach($teacherRole);
        
        $response = $this->actingAs($this->teacher->user)->get(route('teacher.guidance.dashboard'));
        $response->assertStatus(200);
    }
}
