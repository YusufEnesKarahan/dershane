<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Role;
use App\Models\Permission;

class ExamManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create([
            'name' => 'Main Branch',
            'slug' => 'main-branch',
        ]);

        $plan = Plan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'stripe_price_id' => 'price_dummy',
            'features' => ['sms'],
            'limits' => ['max_exams' => 5],
            'price' => 100,
            'interval' => 'month',
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
            'ends_at' => now()->addMonth(),
        ]);

        $this->admin = User::factory()->create(['branch_id' => $this->branch->id]);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $permission = Permission::firstOrCreate(['name' => 'exam.create']);
        Permission::firstOrCreate(['name' => 'exam.view']);
        Permission::firstOrCreate(['name' => 'exam.update']);
        Permission::firstOrCreate(['name' => 'exam.delete']);
        
        // Use relation
        $role->permissions()->attach($permission);
        $role->permissions()->attach(Permission::where('name', 'exam.view')->first());
        $this->admin->roles()->attach($role);
        
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);
    }

    public function test_admin_can_create_exam()
    {
        $this->actingAs($this->admin);

        $course = Course::create([
            'branch_id' => $this->branch->id,
            'name' => 'Math 101',
            'code' => 'MATH101',
            'slug' => 'math-101',
            'credit' => 3
        ]);

        $response = $this->post(route('admin.exams.store'), [
            'title' => 'Midterm Exam',
            'type' => 'mock_exam',
            'exam_date' => now()->addDays(5)->format('Y-m-d'),
            'total_score' => 100,
            'duration_minutes' => 120,
            'subjects' => [
                [
                    'course_id' => $course->id,
                    'question_count' => 50,
                    'max_score' => 100,
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.exams.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('exams', [
            'title' => 'Midterm Exam',
            'branch_id' => $this->branch->id,
        ]);
        
        $this->assertDatabaseHas('exam_subjects', [
            'course_id' => $course->id,
            'question_count' => 50,
        ]);
    }
    
    public function test_admin_cannot_exceed_exam_limit()
    {
        $this->actingAs($this->admin);

        for ($i = 0; $i < 5; $i++) {
            Exam::create([
                'title' => 'Test Exam ' . $i,
                'type' => 'mock_exam',
                'branch_id' => $this->branch->id,
                'created_by' => $this->admin->id,
                'exam_date' => now(),
                'total_score' => 100,
            ]);
        }

        $response = $this->post(route('admin.exams.store'), [
            'title' => 'Over Limit Exam',
            'type' => 'mock_exam',
            'exam_date' => now()->addDays(5)->format('Y-m-d'),
            'total_score' => 100,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('exams', [
            'title' => 'Over Limit Exam'
        ]);
    }
}
