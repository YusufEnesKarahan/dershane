<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('notifications.view');
    }

    public function view(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id || $user->hasPermission('notifications.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('notifications.manage');
    }

    public function update(User $user, Notification $notification): bool
    {
        return $user->hasPermission('notifications.manage');
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $user->hasPermission('notifications.manage');
    }

    public function manage(User $user): bool { return $user->hasPermission('notifications.manage'); }
}
