<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoryItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('inventory.view');
    }

    public function view(User $user, InventoryItem $item): bool
    {
        return $user->hasPermission('inventory.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('inventory.manage');
    }

    public function update(User $user, InventoryItem $item): bool
    {
        return $user->hasPermission('inventory.manage');
    }

    public function delete(User $user, InventoryItem $item): bool
    {
        return $user->hasPermission('inventory.manage');
    }
}
