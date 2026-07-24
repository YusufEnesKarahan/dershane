<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('assets.view') || $user->hasRole('Administrator');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->hasPermission('assets.view') || $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('assets.manage') || $user->hasRole('Administrator');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->hasPermission('assets.manage') || $user->hasRole('Administrator');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->hasPermission('assets.manage') || $user->hasRole('Administrator');
    }
}
