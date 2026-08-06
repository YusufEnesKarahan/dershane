<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_VIEW)
            || $user->hasRole('Super Admin');
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Super Admin')) return true;

        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_VIEW)
            && ($announcement->is_all_branches || $user->branch_id === $announcement->branch_id || $announcement->branches->contains($user->branch_id));
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_CREATE)
            || $user->hasRole('Super Admin');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Super Admin')) return true;

        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_UPDATE)
            && ($announcement->is_all_branches || $user->branch_id === $announcement->branch_id || $announcement->branches->contains($user->branch_id));
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Super Admin')) return true;

        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_DELETE)
            && ($announcement->is_all_branches || $user->branch_id === $announcement->branch_id || $announcement->branches->contains($user->branch_id));
    }
}
