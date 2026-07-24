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
        return $user->hasPermission('assets.view');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->hasPermission('assets.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('assets.manage');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->hasPermission('assets.manage');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->hasPermission('assets.manage');
    }
}
