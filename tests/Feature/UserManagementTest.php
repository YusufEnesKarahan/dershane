<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Domain\UserManagement\Services\UserManagementService;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\PackageSeeder;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected UserManagementService $userManagementService;
    protected Branch $branch1;
    protected Branch $branch2;
    protected $superAdmin;
    protected User $branch1Admin;
    protected User $branch2Admin;
    protected User $teacherUser;
    protected Role $adminRole;
    protected Role $teacherRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(FeatureSeeder::class);
        $this->seed(PackageSeeder::class);
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->userManagementService = app(UserManagementService::class);

        $this->adminRole = Role::firstOrCreate(['name' => 'Branch Admin']);
        $userPerms = \App\Models\Permission::where('name', 'like', 'users.%')->pluck('id');
        $this->adminRole->permissions()->syncWithoutDetaching($userPerms);

        $this->teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Branches
        $this->branch1 = Branch::create([
            'name' => 'Kadıköy Şubesi',
            'code' => 'KDK-01',
            'slug' => 'kdk-01',
            'status' => 'active',
        ]);
        $this->branch2 = Branch::create([
            'name' => 'Beşiktaş Şubesi',
            'code' => 'BSK-01',
            'slug' => 'bsk-01',
            'status' => 'active',
        ]);

        // Users
        $this->superAdmin = User::factory()->create(['branch_id' => null]);
        $this->superAdmin->roles()->attach($superAdminRole);

        $this->branch1Admin = User::factory()->create(['branch_id' => $this->branch1->id]);
        $this->branch1Admin->roles()->attach($this->adminRole);

        $this->branch2Admin = User::factory()->create(['branch_id' => $this->branch2->id]);
        $this->branch2Admin->roles()->attach($this->adminRole);

        $this->teacherUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $this->teacherUser->roles()->attach($this->teacherRole);
    }

    public function test_admin_can_access_user_list(): void
    {
        $response = $this->actingAs($this->branch1Admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Sistem Kullanıcıları');
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->branch1Admin)
            ->post(route('admin.users.store'), [
                'name' => 'Mehmet Demir',
                'email' => 'mehmet.demir@dershane.com',
                'password' => 'secret123',
                'phone' => '05321112233',
                'status' => 'ACTIVE',
                'branch_id' => $this->branch1->id,
                'roles' => [$this->teacherRole->id],
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'mehmet.demir@dershane.com',
            'name' => 'Mehmet Demir',
            'branch_id' => $this->branch1->id,
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $targetUser = User::factory()->create(['branch_id' => $this->branch1->id]);

        $response = $this->actingAs($this->branch1Admin)
            ->put(route('admin.users.update', $targetUser), [
                'name' => 'Mehmet Güncellendi',
                'email' => 'mehmet.guncel@dershane.com',
                'phone' => '05443332211',
                'status' => 'ACTIVE',
                'branch_id' => $this->branch1->id,
                'roles' => [$this->teacherRole->id],
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Mehmet Güncellendi',
            'email' => 'mehmet.guncel@dershane.com',
        ]);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $targetUser = User::factory()->create([
            'branch_id' => $this->branch1->id,
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($this->branch1Admin)
            ->post(route('admin.users.status', $targetUser), [
                'status' => 'PASSIVE',
            ]);

        $response->assertRedirect();
        $this->assertEquals(\App\Enums\UserStatus::PASSIVE, $targetUser->fresh()->status);
    }

    public function test_admin_can_assign_roles_to_user(): void
    {
        $targetUser = User::factory()->create(['branch_id' => $this->branch1->id]);

        $this->userManagementService->assignRoles($targetUser, [$this->teacherRole->id, $this->adminRole->id]);

        $this->assertTrue($targetUser->roles->contains($this->teacherRole->id));
        $this->assertTrue($targetUser->roles->contains($this->adminRole->id));
    }

    public function test_unauthorized_user_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_branch_isolation_restricts_branch_admin_to_own_branch_users(): void
    {
        $userBranch1 = User::factory()->create(['name' => 'Branch1 User', 'branch_id' => $this->branch1->id]);
        $userBranch2 = User::factory()->create(['name' => 'Branch2 User', 'branch_id' => $this->branch2->id]);

        // Branch 1 Admin accessing user list
        $response = $this->actingAs($this->branch1Admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Branch1 User');
        $response->assertDontSee('Branch2 User');

        // Branch 1 Admin trying to edit Branch 2 User should be forbidden (403)
        $editResponse = $this->actingAs($this->branch1Admin)
            ->get(route('admin.users.edit', $userBranch2));

        $editResponse->assertStatus(403);
    }

    public function test_user_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->branch1Admin)
            ->post(route('admin.users.store'), [
                'name' => '',
                'email' => 'invalid-email',
                'password' => '123', // less than 6 chars
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'roles']);
    }
}
