<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudentAdmission;

class AdmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, StudentAdmission $admission): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StudentAdmission $admission): bool
    {
        return true;
    }

    public function approve(User $user, StudentAdmission $admission): bool
    {
        return true;
    }

    public function delete(User $user, StudentAdmission $admission): bool
    {
        return true;
    }
}
