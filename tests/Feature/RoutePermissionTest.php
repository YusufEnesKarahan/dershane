<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoutePermissionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $unauthorizedUser;
    protected User $authorizedUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // 2. Users
        $this->unauthorizedUser = User::create([
            'name' => 'No Perm User',
            'email' => 'norouteperms@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->unauthorizedUser->roles()->attach($parentRole->id);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'adminrouteperms@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        $this->adminUser->roles()->attach($adminRole->id);

        $this->authorizedUser = User::create([
            'name' => 'Route Perm User',
            'email' => 'hasrouteperms@test.com',
            'password' => bcrypt('password'),
            'status' => \App\Enums\UserStatus::ACTIVE
        ]);
        
        $permissions = [
            'students.view',
            'teachers.view',
            'classrooms.view',
            'hr.view',
            'notifications.view'
        ];

        $testRole = Role::firstOrCreate(['name' => 'TestRouteAuthorizedRole']);
        
        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName]);
            $testRole->permissions()->syncWithoutDetaching([$perm->id]);
        }
        
        $this->authorizedUser->roles()->attach($testRole->id);

        // Clear permissions cache
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->unauthorizedUser);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->authorizedUser);
        app(\App\Domain\Auth\Services\PermissionCache::class)->clearUserCache($this->adminUser);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_gets_403(): void
    {
        $this->actingAs($this->unauthorizedUser);

        // Accessing students index
        $response1 = $this->get(route('admin.students.index'));
        $response1->assertStatus(403);

        // Accessing teachers index
        $response2 = $this->get(route('admin.teachers.index'));
        $response2->assertStatus(403);
    }

    public function test_authorized_user_can_access(): void
    {
        $this->actingAs($this->authorizedUser);

        // Accessing students index
        $response1 = $this->get(route('admin.students.index'));
        $response1->assertStatus(200);

        // Accessing teachers index
        $response2 = $this->get(route('admin.teachers.index'));
        $response2->assertStatus(200);

        // Accessing HR dashboard
        $response3 = $this->get(route('admin.hr.dashboard'));
        $response3->assertStatus(200);
    }

    public function test_administrator_can_access_all(): void
    {
        $this->actingAs($this->adminUser);

        // Accessing students index
        $response1 = $this->get(route('admin.students.index'));
        $response1->assertStatus(200);

        // Accessing teachers index
        $response2 = $this->get(route('admin.teachers.index'));
        $response2->assertStatus(200);

        // Accessing HR dashboard
        $response3 = $this->get(route('admin.hr.dashboard'));
        $response3->assertStatus(200);
    }
}
