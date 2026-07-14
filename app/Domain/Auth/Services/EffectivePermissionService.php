<?php
namespace App\Domain\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class EffectivePermissionService
{
    public function __construct(protected PermissionCache $permissionCache) {}

    public function effectivePermissions(User $user): array
    {
        $cacheKey = 'effective_permissions_user_' . $user->id;

        return Cache::rememberForever($cacheKey, function () use ($user) {
            // Unifies role permissions
            return $this->permissionCache->getUserPermissions($user);
        });
    }

    public function clearCache(User $user): void
    {
        Cache::forget('effective_permissions_user_' . $user->id);
    }
}
