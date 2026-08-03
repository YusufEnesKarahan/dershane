<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendanceSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('attendance.view');
    }

    public function view(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission('attendance.view') && 
               $session->branch_id === \App\Core\Context\TenantContext::getActiveBranchId();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('attendance.create');
    }

    public function update(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission('attendance.update') &&
               $session->branch_id === \App\Core\Context\TenantContext::getActiveBranchId();
    }

    public function delete(User $user, AttendanceSession $session): bool
    {
        return $user->hasPermission('attendance.create') &&
               $session->branch_id === \App\Core\Context\TenantContext::getActiveBranchId();
    }
    
    public function report(User $user): bool
    {
        return $user->hasPermission('attendance.report');
    }
}
