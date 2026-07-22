<?php
namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Assignment $assignment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return true;
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return true;
    }
}
