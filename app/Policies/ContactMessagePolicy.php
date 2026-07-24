<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContactMessage;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('contacts.view');
    }

    public function view(User $user, ContactMessage $model): bool
    {
        return $user->hasPermission('contacts.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('contacts.create');
    }

    public function update(User $user, ContactMessage $model): bool
    {
        return $user->hasPermission('contacts.update');
    }

    public function delete(User $user, ContactMessage $model): bool
    {
        return $user->hasPermission('contacts.delete');
    }
}
