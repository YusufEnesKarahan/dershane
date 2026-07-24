<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('documents.view') || $user->hasRole('Administrator');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.view') || $user->hasRole('Administrator');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('documents.manage') || $user->hasRole('Administrator');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.manage') || $user->hasRole('Administrator');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.manage') || $user->hasRole('Administrator');
    }
}
