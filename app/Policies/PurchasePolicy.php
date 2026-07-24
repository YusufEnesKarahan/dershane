<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PurchaseOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('purchase.view') || $user->hasRole('Administrator');
    }

    public function view(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission('purchase.view') || $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('purchase.manage') || $user->hasRole('Administrator');
    }

    public function update(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission('purchase.manage') || $user->hasRole('Administrator');
    }

    public function delete(User $user, PurchaseOrder $order): bool
    {
        return $user->hasPermission('purchase.manage') || $user->hasRole('Administrator');
    }
}
