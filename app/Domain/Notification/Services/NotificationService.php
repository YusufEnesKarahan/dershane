<?php

namespace App\Domain\Notification\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Teacher;
use App\Domain\Notification\Enums\NotificationType;
use App\Domain\Notification\Enums\NotificationChannel;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function send(User|iterable $users, string $title, string $content, NotificationType $type = NotificationType::SYSTEM, array $channels = [NotificationChannel::DATABASE])
    {
        $channels = array_map(fn($channel) => $channel->value, $channels);
        Notification::send($users, new GeneralNotification($title, $content, $type->value, $channels));
    }

    public function sendToParent(StudentGuardian $guardian, string $title, string $content, NotificationType $type = NotificationType::SYSTEM)
    {
        if ($guardian->user) {
            $this->send($guardian->user, $title, $content, $type);
        }
    }

    public function sendToStudent(Student $student, string $title, string $content, NotificationType $type = NotificationType::SYSTEM)
    {
        if ($student->user) {
            $this->send($student->user, $title, $content, $type);
        }
    }

    public function sendToTeacher(Teacher $teacher, string $title, string $content, NotificationType $type = NotificationType::SYSTEM)
    {
        if ($teacher->user) {
            $this->send($teacher->user, $title, $content, $type);
        }
    }

    public function markAsRead(User $user, string $notificationId = null)
    {
        if ($notificationId) {
            $notification = $user->unreadNotifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        } else {
            $user->unreadNotifications()->update(['read_at' => now(), 'status' => 'Read']);
        }
    }

    public function getUnread(User $user)
    {
        return $user->unreadNotifications;
    }
}
