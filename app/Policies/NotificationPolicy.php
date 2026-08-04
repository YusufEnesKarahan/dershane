<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('notification.view') || $user->hasPermission('notifications.view') || $user->hasRole(['Admin', 'Super Admin', 'Teacher', 'Student', 'Parent']);
    }

    public function view(User $user, Notification $notification): bool
    {
        if ($notification->branch_id && $user->branch_id && $notification->branch_id !== $user->branch_id) {
            return false;
        }

        if ($user->hasRole(['Admin', 'Super Admin']) || $user->hasPermission('notification.manage') || $user->hasPermission('notifications.manage')) {
            return true;
        }

        if ($notification->receiver_id === $user->id || $notification->user_id === $user->id) {
            return true;
        }

        if ($user->hasRole('Parent')) {
            $student = $user->guardian?->students()->first();
            if ($student && ($notification->receiver_id === $student->user_id || $notification->user_id === $student->user_id)) {
                return true;
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('notification.create') || $user->hasPermission('notification.send') || $user->hasPermission('notifications.manage') || $user->hasRole(['Admin', 'Super Admin', 'Teacher']);
    }

    public function send(User $user): bool
    {
        return $user->hasPermission('notification.send') || $user->hasPermission('notifications.manage') || $user->hasRole(['Admin', 'Super Admin', 'Teacher']);
    }

    public function update(User $user, Notification $notification): bool
    {
        return $this->view($user, $notification);
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $user->hasRole(['Admin', 'Super Admin']) || $user->hasPermission('notification.manage') || $user->hasPermission('notifications.manage');
    }

    public function manage(User $user): bool
    {
        return $user->hasRole(['Admin', 'Super Admin']) || $user->hasPermission('notification.manage') || $user->hasPermission('notifications.manage');
    }
}
