<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Domain\Auth\Dictionaries\PermissionDictionary;
use App\Domain\Auth\Services\PermissionCache;
use App\Domain\Auth\Services\RoleManager;
use Illuminate\Support\Facades\Route;

class RbacConsistencyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure default roles are seeded for the test if not running a fresh seed
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_all_dictionary_permissions_exist_in_database(): void
    {
        $dictionaryPerms = PermissionDictionary::all();
        $dbPerms = Permission::pluck('name')->toArray();

        foreach ($dictionaryPerms as $perm) {
            $this->assertContains($perm, $dbPerms, "Permission '{$perm}' from Dictionary is missing in database.");
        }
    }

    public function test_all_route_permissions_exist_in_dictionary(): void
    {
        $routes = Route::getRoutes()->getRoutes();
        $dictionaryPerms = PermissionDictionary::all();

        foreach ($routes as $route) {
            $middlewares = $route->middleware();
            foreach ($middlewares as $middleware) {
                if (is_string($middleware) && str_starts_with($middleware, 'permission:')) {
                    $permString = str_replace('permission:', '', $middleware);
                    // Handle dynamic assignments like `permission:users.view|users.create`
                    $perms = explode('|', $permString);
                    foreach ($perms as $p) {
                        $this->assertContains($p, $dictionaryPerms, "Route {$route->uri()} requires '{$p}' which is not in PermissionDictionary.");
                    }
                }
            }
        }
    }

    public function test_menu_permissions_match_dictionary(): void
    {
        $menuConfig = config('admin-menu.menu');
        $dictionaryPerms = PermissionDictionary::all();

        $checkMenu = function($items) use (&$checkMenu, $dictionaryPerms) {
            foreach ($items as $item) {
                if (isset($item['permission'])) {
                    $this->assertContains($item['permission'], $dictionaryPerms, "Menu item '{$item['title']}' uses unknown permission '{$item['permission']}'.");
                }
                if (isset($item['children'])) {
                    $checkMenu($item['children']);
                }
            }
        };

        $checkMenu($menuConfig);
    }

    public function test_cache_is_cleared_when_role_permissions_change(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Test Role']);
        $user->roles()->attach($role->id);

        $perm1 = Permission::firstOrCreate(['name' => PermissionDictionary::USERS_VIEW]);
        $perm2 = Permission::firstOrCreate(['name' => PermissionDictionary::USERS_CREATE]);

        // Assign first permission and build cache
        app(RoleManager::class)->assignPermissionToRole($role, [$perm1->id]);
        
        // Check effective permissions
        $this->assertTrue($user->hasPermission(PermissionDictionary::USERS_VIEW));
        $this->assertFalse($user->hasPermission(PermissionDictionary::USERS_CREATE));

        // Assign second permission. The RoleManager should clear cache.
        app(RoleManager::class)->assignPermissionToRole($role, [$perm2->id]);
        
        // Assert cache was cleared and newly assigned permission is effective
        $this->assertTrue($user->hasPermission(PermissionDictionary::USERS_CREATE));
    }

    public function test_super_admin_bypasses_permission_checks(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $user->roles()->attach($role->id);

        // Ensure user has no direct permissions
        $this->assertTrue($user->hasPermission('some.random.permission'));
        $this->assertTrue($user->hasPermission(PermissionDictionary::USERS_DELETE));
    }

    public function test_normal_user_is_restricted_to_assigned_permissions(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Standard User']);
        $user->roles()->attach($role->id);

        $perm = Permission::firstOrCreate(['name' => PermissionDictionary::DASHBOARD_VIEW]);
        app(RoleManager::class)->assignPermissionToRole($role, [$perm->id]);

        $this->assertTrue($user->hasPermission(PermissionDictionary::DASHBOARD_VIEW));
        $this->assertFalse($user->hasPermission(PermissionDictionary::USERS_VIEW));
    }
}
