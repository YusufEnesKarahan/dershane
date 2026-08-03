<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Announcement;
use App\Domain\Notification\Enums\NotificationType;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Cache;

class NotificationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $studentUser;
    protected $parentUser;
    protected $otherBranchUser;
    protected $branch;
    protected $otherBranch;

    protected function setUp(): void
    {
        parent::setUp();
        
        Cache::flush();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create Branch first (Branch model requires a slug)
        $this->branch = Branch::factory()->create([
            'slug' => 'test-branch-' . uniqid(),
            'name' => 'Test Branch'
        ]);
        $this->otherBranch = Branch::factory()->create(['slug' => 'other-branch-'.uniqid(), 'name' => 'Other Branch']);

        // Create Admin
        $this->admin = User::factory()->create(['branch_id' => $this->branch->id, 'status' => UserStatus::ACTIVE]);
        $adminRole = Role::where('name', 'Admin')->first();
        $this->admin->roles()->attach($adminRole->id);

        // Create Student User
        $this->studentUser = User::factory()->create(['branch_id' => $this->branch->id, 'status' => UserStatus::ACTIVE]);
        $studentRole = Role::where('name', 'Student')->first();
        $this->studentUser->roles()->attach($studentRole->id);

        // Create Parent User
        $this->parentUser = User::factory()->create(['branch_id' => $this->branch->id, 'status' => UserStatus::ACTIVE]);
        $parentRole = Role::where('name', 'Parent')->first();
        $this->parentUser->roles()->attach($parentRole->id);

        // Create Other Branch User (Admin)
        $this->otherBranchUser = User::factory()->create(['branch_id' => $this->otherBranch->id, 'status' => UserStatus::ACTIVE]);
        $this->otherBranchUser->roles()->attach($adminRole->id);

        // Bypass CheckOnboardingStatus
        \App\Models\SystemIdentity::create(['name' => 'Test System']);
        \App\Models\AcademicTerm::create([
            'name' => 'Test Term',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_active' => true
        ]);
    }

    // 1. Tenant admin duyuru oluşturabilir.
    public function test_admin_can_create_announcement()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.announcements.store'), [
            'title' => 'Test Announcement',
            'content' => 'This is a test announcement',
            'type' => 'announcement',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseHas('announcements', [
            'title' => 'Test Announcement',
            'branch_id' => $this->branch->id,
            'status' => 'draft',
        ]);
    }

    // 2. Tenant admin kendi branch duyurularını görebilir.
    public function test_admin_can_view_own_branch_announcements()
    {
        Announcement::create([
            'branch_id' => $this->branch->id,
            'title' => 'Own Branch Announcement',
            'content' => 'Test Content',
            'type' => 'announcement',
            'created_by' => $this->admin->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Own Branch Announcement');
    }

    // 3. Tenant A kullanıcısı Tenant B duyurusunu göremez.
    public function test_admin_cannot_view_other_branch_announcements()
    {
        Announcement::create([
            'branch_id' => $this->otherBranch->id,
            'title' => 'Other Branch Announcement',
            'content' => 'Test Content',
            'type' => 'announcement',
            'created_by' => $this->otherBranchUser->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.announcements.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Other Branch Announcement');
    }

    // 4. Parent sadece kendi çocuğuna ait bildirimleri görür (Burada role-based Parent branch bildirimi).
    public function test_parent_sees_own_notifications()
    {
        $this->withoutExceptionHandling();
        $announcement = Announcement::create([
            'branch_id' => $this->branch->id,
            'title' => 'Parents Announcement',
            'content' => 'Test Content',
            'type' => 'announcement',
            'created_by' => $this->admin->id,
            'target_role' => 'Parent',
            'status' => 'draft',
        ]);

        $this->actingAs($this->admin)->post(route('admin.announcements.publish', $announcement));

        $this->assertEquals(1, $this->parentUser->fresh()->notifications()->count());
        $this->assertEquals(0, $this->studentUser->fresh()->notifications()->count());
    }

    // 5. Student sadece kendi bildirimlerini görür.
    public function test_student_sees_own_notifications()
    {
        $announcement = Announcement::create([
            'branch_id' => $this->branch->id,
            'title' => 'Student Announcement',
            'content' => 'Test Content',
            'type' => 'announcement',
            'created_by' => $this->admin->id,
            'target_role' => 'Student',
            'status' => 'draft',
        ]);

        $this->actingAs($this->admin)->post(route('admin.announcements.publish', $announcement));

        $this->assertEquals(1, $this->studentUser->fresh()->notifications()->count());
        $this->assertEquals(0, $this->parentUser->fresh()->notifications()->count());
    }

    // 6. Teacher/Student duyuru oluşturamaz.
    public function test_student_cannot_create_announcement()
    {
        $response = $this->actingAs($this->studentUser)->post(route('admin.announcements.store'), [
            'title' => 'Test Announcement',
            'content' => 'This is a test announcement',
            'type' => 'announcement',
        ]);

        $response->assertForbidden();
    }

    // 7. RBAC olmayan kullanıcı erişemez.
    public function test_user_without_rbac_cannot_access_announcements()
    {
        $guestUser = User::factory()->create(['branch_id' => $this->branch->id, 'status' => UserStatus::ACTIVE]);
        // No roles attached

        $response = $this->actingAs($guestUser)->get(route('admin.announcements.index'));
        $response->assertForbidden();
    }

    // Ek test: read notification
    public function test_student_can_read_notification()
    {
        $announcement = Announcement::create([
            'branch_id' => $this->branch->id,
            'title' => 'Student Announcement',
            'content' => 'Test Content',
            'type' => 'announcement',
            'created_by' => $this->admin->id,
            'target_role' => 'Student',
            'status' => 'draft',
        ]);

        $this->actingAs($this->admin)->post(route('admin.announcements.publish', $announcement));

        $notification = $this->studentUser->fresh()->notifications()->first();
        $this->assertNull($notification->read_at);

        $response = $this->actingAs($this->studentUser)->post(route('student.notifications.read', $notification->id));
        
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
