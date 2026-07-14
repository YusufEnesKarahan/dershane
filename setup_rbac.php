<?php

function createClass($path, $content) {
    $dir = dirname(__DIR__ . '/' . $path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(__DIR__ . '/' . $path, $content);
}

// RBAC Domain
$services = [
    'PermissionCache' => "<?php
namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class PermissionCache
{
    public function getUserPermissions(User \$user): array
    {
        \$cacheKey = 'user_permissions_' . \$user->id;
        
        return Cache::rememberForever(\$cacheKey, function () use (\$user) {
            return \$user->roles()->with('permissions')->get()
                ->pluck('permissions')->flatten()->pluck('name')->unique()->toArray();
        });
    }

    public function clearUserCache(User \$user): void
    {
        Cache::forget('user_permissions_' . \$user->id);
    }

    public function clearRoleCache(\$role): void
    {
        // When a role changes permissions, we clear cache for all users with that role
        foreach (\$role->users as \$user) {
            \$this->clearUserCache(\$user);
        }
    }
}
",
    'AuthorizationService' => "<?php
namespace App\Domain\Auth\Services;

use App\Models\User;

class AuthorizationService
{
    public function __construct(protected PermissionCache \$permissionCache) {}

    public function hasRole(User \$user, string|array \$roles): bool
    {
        \$userRoles = \$user->roles->pluck('name')->toArray();
        \$checkRoles = is_array(\$roles) ? \$roles : [\$roles];
        
        return count(array_intersect(\$checkRoles, \$userRoles)) > 0;
    }

    public function hasPermission(User \$user, string \$permission): bool
    {
        if (\$this->hasRole(\$user, 'Administrator')) {
            return true;
        }

        \$userPermissions = \$this->permissionCache->getUserPermissions(\$user);
        
        // Handle wildcard permissions like 'users.*'
        foreach (\$userPermissions as \$userPerm) {
            if (\$userPerm === \$permission) {
                return true;
            }
            if (str_ends_with(\$userPerm, '.*')) {
                \$prefix = substr(\$userPerm, 0, -1);
                if (str_starts_with(\$permission, \$prefix)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
",
    'PermissionManager' => "<?php
namespace App\Domain\Auth\Services;

use App\Models\Permission;

class PermissionManager
{
    public function createPermission(string \$name): Permission
    {
        return Permission::firstOrCreate(['name' => \$name]);
    }
}
",
    'RoleManager' => "<?php
namespace App\Domain\Auth\Services;

use App\Models\Role;

class RoleManager
{
    public function __construct(protected PermissionCache \$cache) {}

    public function assignPermissionToRole(Role \$role, string|array \$permissions): void
    {
        \$role->permissions()->syncWithoutDetaching(\$permissions);
        \$this->cache->clearRoleCache(\$role);
    }

    public function revokePermissionFromRole(Role \$role, string|array \$permissions): void
    {
        \$role->permissions()->detach(\$permissions);
        \$this->cache->clearRoleCache(\$role);
    }
}
",
    'PolicyResolver' => "<?php
namespace App\Domain\Auth\Services;

class PolicyResolver
{
    // Resolves logic dynamically if needed.
    // For now, simple registration mapping is handled in AuthServiceProvider.
}
"
];

foreach ($services as $name => $content) {
    createClass('app/Domain/Auth/Services/' . $name . '.php', $content);
}

// Middleware
$middlewares = [
    'PermissionMiddleware' => "<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Auth\Services\AuthorizationService;

class PermissionMiddleware
{
    public function __construct(protected AuthorizationService \$authService) {}

    public function handle(Request \$request, Closure \$next, string \$permission): Response
    {
        if (! \$request->user() || ! \$this->authService->hasPermission(\$request->user(), \$permission)) {
            abort(403, 'Unauthorized action.');
        }

        return \$next(\$request);
    }
}
",
    'RoleMiddleware' => "<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Auth\Services\AuthorizationService;

class RoleMiddleware
{
    public function __construct(protected AuthorizationService \$authService) {}

    public function handle(Request \$request, Closure \$next, string \$role): Response
    {
        if (! \$request->user() || ! \$this->authService->hasRole(\$request->user(), explode('|', \$role))) {
            abort(403, 'Unauthorized action.');
        }

        return \$next(\$request);
    }
}
",
    'EditionMiddleware' => "<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EditionMiddleware
{
    public function handle(Request \$request, Closure \$next, string \$edition): Response
    {
        // Simple edition check using helper from sprint 1.1.1
        if (! function_exists('edition') || edition()->current() !== \$edition) {
            abort(403, 'This feature is not available in your current edition.');
        }

        return \$next(\$request);
    }
}
"
];

foreach ($middlewares as $name => $content) {
    createClass('app/Http/Middleware/' . $name . '.php', $content);
}

// Policies
$models = ['User', 'Role', 'Page', 'Blog', 'Teacher', 'Course', 'Registration', 'Lead', 'ContactMessage'];

$plurals = [
    'User' => 'users',
    'Role' => 'roles',
    'Page' => 'pages',
    'Blog' => 'blogs',
    'Teacher' => 'teachers',
    'Course' => 'courses',
    'Registration' => 'registrations',
    'Lead' => 'leads',
    'ContactMessage' => 'contactmessages',
];

foreach ($models as $model) {
    $permPrefix = $plurals[$model];
    createClass('app/Policies/' . $model . 'Policy.php', "<?php
namespace App\Policies;

use App\Models\User;
use App\Models\\{$model};
use App\Domain\Auth\Services\AuthorizationService;

class {$model}Policy
{
    public function __construct(protected AuthorizationService \$authService) {}

    public function viewAny(User \$user): bool
    {
        return \$this->authService->hasPermission(\$user, '{$permPrefix}.view');
    }

    public function view(User \$user, {$model} \$model): bool
    {
        return \$this->authService->hasPermission(\$user, '{$permPrefix}.view');
    }

    public function create(User \$user): bool
    {
        return \$this->authService->hasPermission(\$user, '{$permPrefix}.create');
    }

    public function update(User \$user, {$model} \$model): bool
    {
        return \$this->authService->hasPermission(\$user, '{$permPrefix}.update');
    }

    public function delete(User \$user, {$model} \$model): bool
    {
        return \$this->authService->hasPermission(\$user, '{$permPrefix}.delete');
    }
}
");
}

echo "RBAC Services, Middlewares, and Policies created.\n";
