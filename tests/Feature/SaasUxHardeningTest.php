<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Role;
use App\Models\PlatformAuditLog;
use App\Models\Notification;
use App\Core\Context\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaasUxHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $studentUser;
    protected $testBranch1;
    protected $testBranch2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOnboardingCompleted::class,
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $rSuperAdmin = Role::where('name', 'Super Admin')->first();
        $rStudent = Role::where('name', 'Student')->first();

        // Create branches
        $this->testBranch1 = Branch::create([
            'name' => 'Kıyı Şubesi',
            'slug' => 'kiyi-subesi',
            'phone' => '5550001122',
            'email' => 'kiyi@test.com',
            'address' => 'Kıyı Mah.'
        ]);

        $this->testBranch2 = Branch::create([
            'name' => 'Dağ Şubesi',
            'slug' => 'dag-subesi',
            'phone' => '5550003344',
            'email' => 'dag@test.com',
            'address' => 'Dağ Mah.'
        ]);

        // Create Users
        $this->adminUser = User::factory()->create(['branch_id' => $this->testBranch1->id]);
        $this->adminUser->roles()->sync([$rSuperAdmin->id]);

        $this->studentUser = User::factory()->create(['branch_id' => $this->testBranch1->id]);
        $this->studentUser->roles()->sync([$rStudent->id]);
    }

    public function test_empty_dashboard_loads_successfully()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->testBranch1->id]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_unauthorized_pages_return_correct_status()
    {
        $this->actingAs($this->studentUser)
             ->withSession(['active_branch_id' => $this->testBranch1->id]);

        $response = $this->get(route('admin.students.index'));
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_empty_collections_dont_crash()
    {
        $this->actingAs($this->adminUser)
             ->withSession(['active_branch_id' => $this->testBranch1->id]);

        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.teachers.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.classrooms.index'));
        $response->assertStatus(200);
    }

    public function test_dashboard_respects_tenant_isolation()
    {
        // Student in Branch 1
        $s1 = Student::create([
            'branch_id' => $this->testBranch1->id,
            'student_number' => 'ISOLATE-1',
            'first_name' => 'Ali',
            'last_name' => 'Veli'
        ]);

        // Student in Branch 2
        $s2 = Student::create([
            'branch_id' => $this->testBranch2->id,
            'student_number' => 'ISOLATE-2',
            'first_name' => 'Ayşe',
            'last_name' => 'Fatma'
        ]);

        TenantContext::setActiveBranchId($this->testBranch1->id);
        $this->assertEquals(1, Student::count());
        $this->assertEquals('Ali', Student::first()->first_name);

        TenantContext::setActiveBranchId($this->testBranch2->id);
        $this->assertEquals(1, Student::count());
        $this->assertEquals('Ayşe', Student::first()->first_name);

        TenantContext::clear();
    }

    public function test_notification_creation_works()
    {
        $notification = Notification::create([
            'user_id' => $this->adminUser->id,
            'title' => 'Sistem Güncellemesi',
            'message' => 'Sistem bakımı başarıyla tamamlandı.',
            'type' => 'system',
            'status' => 'Unread'
        ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'title' => 'Sistem Güncellemesi'
        ]);
    }

    public function test_activity_log_creation_works()
    {
        $log = PlatformAuditLog::record(
            $this->adminUser,
            'student.created',
            'Student',
            ['student_number' => 'STU-999']
        );

        $this->assertDatabaseHas('platform_audit_logs', [
            'id' => $log->id,
            'action' => 'student.created'
        ]);
    }
}
