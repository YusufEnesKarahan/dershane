<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use App\Domain\Auth\Dictionaries\PermissionDictionary;
use Illuminate\Auth\Access\Response;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_VIEW);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_VIEW)
            && $user->branch_id === $announcement->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_CREATE);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_UPDATE)
            && $user->branch_id === $announcement->branch_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission(PermissionDictionary::ANNOUNCEMENTS_DELETE)
            && $user->branch_id === $announcement->branch_id;
    }

    public function restore(User $user, Announcement $announcement): bool
    {
        return false;
    }

    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return false;
    }
}
