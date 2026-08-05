<?php

namespace Tests\Feature;

use App\Domain\Academic\Services\AcademicProfessionalService;
use App\Models\AcademicTerm;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemIdentity;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicProfessionalizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected $branch;
    protected Teacher $teacher1;
    protected Teacher $teacher2;
    protected Classroom $classroom;
    protected AcademicTerm $term;

    protected function setUp(): void
    {
        parent::setUp();

        SystemIdentity::firstOrCreate(['company_name' => 'Test'], ['product_name' => 'Test ERP']);
        $this->term = AcademicTerm::firstOrCreate(['name' => '2025-2026'], ['start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->branch = Branch::create(['name' => 'Kadıköy Şube', 'slug' => 'kadikoy-' . uniqid()]);
        
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $this->adminUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->adminUser->roles()->attach($roleAdmin);

        $u1 = User::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Ahmet Hoca']);
        $u2 = User::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Zeynep Hoca']);

        $this->teacher1 = Teacher::create(['branch_id' => $this->branch->id, 'user_id' => $u1->id, 'employment_type' => 'full_time']);
        $this->teacher2 = Teacher::create(['branch_id' => $this->branch->id, 'user_id' => $u2->id, 'employment_type' => 'part_time']);

        $this->classroom = Classroom::create(['branch_id' => $this->branch->id, 'code' => 'CLS-12A-' . uniqid(), 'name' => '12-A TM', 'capacity' => 30]);
    }

    public function test_course_supports_multi_teacher_assignment_with_primary_and_assistant(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'code' => 'MAT-101',
            'name' => 'Matematik - AYT',
            'price' => 5000,
            'primary_teacher_id' => $this->teacher1->id,
            'assistant_teacher_ids' => [$this->teacher2->id],
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHas('success');

        $course = Course::where('code', 'MAT-101')->firstOrFail();
        $this->assertCount(2, $course->teachers);

        $primary = $course->primaryTeacher();
        $this->assertNotNull($primary);
        $this->assertEquals($this->teacher1->id, $primary->id);

        $assistants = $course->assistantTeachers()->get();
        $this->assertCount(1, $assistants);
        $this->assertEquals($this->teacher2->id, $assistants->first()->id);
    }

    public function test_weekly_study_program_creation_with_rich_fields(): void
    {
        $course = Course::create(['code' => 'FIZ-101', 'slug' => 'fizik-101', 'name' => 'Fizik', 'capacity' => 20]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.homeworks.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $this->teacher1->id,
            'title' => '3. Hafta Fizik - Vektörler',
            'week_number' => 3,
            'subject' => 'Vektörler ve Kuvvet',
            'source_book' => 'Palme AYT Fizik',
            'page_range' => '45 - 58',
            'video_url' => 'https://youtube.com/watch?v=test',
            'priority' => 'high',
            'estimated_minutes' => 90,
            'due_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.homeworks.index'));
        $response->assertSessionHas('success');

        $hw = Homework::where('title', '3. Hafta Fizik - Vektörler')->firstOrFail();
        $this->assertEquals(3, $hw->week_number);
        $this->assertEquals('Palme AYT Fizik', $hw->source_book);
        $this->assertEquals('high', $hw->priority);
        $this->assertEquals(90, $hw->estimated_minutes);
    }

    public function test_student_task_progress_updating_and_percentage_calculation(): void
    {
        $student = Student::create([
            'branch_id' => $this->branch->id,
            'student_number' => 'STU-99',
            'first_name' => 'Ali',
            'last_name' => 'Kaya',
            'classroom_id' => $this->classroom->id,
        ]);

        $course = Course::create(['code' => 'KIM-101', 'slug' => 'kimya-101', 'name' => 'Kimya', 'capacity' => 20]);

        $hw = Homework::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $course->id,
            'teacher_id' => $this->teacher1->id,
            'title' => 'Organik Kimya Çalışması',
            'week_number' => 4,
            'due_date' => now()->addDays(5),
            'status' => 'published',
        ]);

        $sub = HomeworkSubmission::create([
            'branch_id' => $this->branch->id,
            'homework_id' => $hw->id,
            'student_id' => $student->id,
            'task_status' => 'Not Started',
            'progress_percentage' => 0,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.homeworks.update-task-progress', $sub->id), [
            'task_status' => 'Completed',
        ]);

        $response->assertRedirect();
        $sub->refresh();

        $this->assertEquals('Completed', $sub->task_status);
        $this->assertEquals(100, $sub->progress_percentage);
    }

    public function test_13_branch_results_net_calculation(): void
    {
        $student = Student::create([
            'branch_id' => $this->branch->id,
            'student_number' => 'STU-100',
            'first_name' => 'Buse',
            'last_name' => 'Öztürk',
        ]);

        $exam = Exam::create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->adminUser->id,
            'title' => 'TYT Deneme #1',
            'type' => 'TYT',
            'exam_date' => now(),
            'total_score' => 500,
        ]);

        $examResult = ExamResult::create([
            'branch_id' => $this->branch->id,
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'score' => 385.50,
        ]);

        $branchesData = [
            'Türkçe' => ['correct' => 32, 'wrong' => 8, 'empty' => 0], // Net = 32 - 2 = 30
            'Matematik' => ['correct' => 25, 'wrong' => 4, 'empty' => 11], // Net = 25 - 1 = 24
            'Fen' => ['correct' => 15, 'wrong' => 4, 'empty' => 1], // Net = 15 - 1 = 14
        ];

        $service = new AcademicProfessionalService();
        $service->saveExamBranchResults($examResult, $branchesData, 'TYT');

        $examResult->refresh();

        $this->assertEquals(68, $examResult->total_net); // 30 + 24 + 14 = 68
        $this->assertCount(3, $examResult->branchResults);

        $turkceRes = $examResult->branchResults()->where('branch_name', 'Türkçe')->first();
        $this->assertEquals(30.00, $turkceRes->net_count);
    }

    public function test_student_academic_analytics_view_renders_properly(): void
    {
        $student = Student::create([
            'branch_id' => $this->branch->id,
            'student_number' => 'STU-101',
            'first_name' => 'Veli',
            'last_name' => 'Yılmaz',
            'classroom_id' => $this->classroom->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.students.academic-analytics', $student->id));

        $response->assertOk();
        $response->assertSee('Akademik Gelişim Paneli');
        $response->assertSee('Veli Yılmaz');
    }
}
