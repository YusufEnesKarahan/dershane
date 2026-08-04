<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\AcademicTerm;
use App\Models\LessonSchedule;
use App\Models\Subscription;
use App\Models\Plan;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckOnboardingStatus::class);

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

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@dershane.com',
            'password' => bcrypt('password'),
            'branch_id' => $this->branch->id
        ]);
        
        $role = Role::create(['name' => 'Admin']);
        $this->admin->roles()->attach($role);
        
        // Setup subscription
        $plan = Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'price' => 100,
            'interval' => 'month',
            'limits' => ['max_schedules' => 100]
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

    public function test_admin_can_create_schedule()
    {
        // Add permission
        $role = $this->admin->roles()->first();
        $role->permissions()->create(['name' => 'schedules.create']);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->admin);

        $response = $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '08:00',
            'end_time' => '08:45',
            'room' => 'Room 101'
        ]);

        $response->assertRedirect(route('admin.schedules.index'));
        $this->assertDatabaseHas('lesson_schedules', [
            'day_of_week' => 'Monday',
            'start_time' => '08:00',
            'room' => 'Room 101'
        ]);
    }

    public function test_teacher_conflict_prevents_schedule_creation()
    {
        // Add permission
        $role = $this->admin->roles()->first();
        $role->permissions()->create(['name' => 'schedules.create']);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->admin);

        LessonSchedule::create([
            'branch_id' => $this->branch->id,
            'academic_term_id' => $this->term->id,
            'classroom_id' => $this->classroom->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '08:00',
            'end_time' => '08:45',
            'room' => 'Room 101'
        ]);

        $classroom2 = Classroom::create(['branch_id' => $this->branch->id, 'name' => '12-B', 'code' => '12B', 'capacity' => 20]);

        $response = $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
            'academic_term_id' => $this->term->id,
            'classroom_id' => $classroom2->id,
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 'Monday',
            'start_time' => '08:15',
            'end_time' => '09:00',
            'room' => 'Room 102'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('lesson_schedules', 1);
    }
}
