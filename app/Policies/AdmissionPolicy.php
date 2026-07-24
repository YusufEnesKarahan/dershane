<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudentAdmission;

class AdmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admission.view');
    }

    public function view(User $user, StudentAdmission $admission): bool
    {
        return $user->hasPermission('admission.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admission.create');
    }

    public function update(User $user, StudentAdmission $admission): bool
    {
        return $user->hasPermission('admission.update');
    }

    public function approve(User $user, StudentAdmission $admission): bool
    {
        return $user->hasPermission('admission.approve');
    }

    public function delete(User $user, StudentAdmission $admission): bool
    {
        return $user->hasPermission('enrollment.manage');
    }
}
