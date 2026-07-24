<?php

namespace App\Policies;

use App\Models\User;

class ReportingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('dashboard.view');
    }

    public function manage(User $user): bool
    {
        return $user->hasPermission('dashboard.view');
    }
}
