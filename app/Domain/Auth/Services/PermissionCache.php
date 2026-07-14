<?php
namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class PermissionCache
{
    public function getUserPermissions(User $user): array
    {
        $cacheKey = 'user_permissions_' . $user->id;
        
        return Cache::rememberForever($cacheKey, function () use ($user) {
            return $user->roles()->with('permissions')->get()
                ->pluck('permissions')->flatten()->pluck('name')->unique()->toArray();
        });
    }

    public function clearUserCache(User $user): void
    {
        Cache::forget('user_permissions_' . $user->id);
    }

    public function clearRoleCache($role): void
    {
        // When a role changes permissions, we clear cache for all users with that role
        foreach ($role->users as $user) {
            $this->clearUserCache($user);
        }
    }
}
