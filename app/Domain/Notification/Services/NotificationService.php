<?php

namespace App\Domain\Notification\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\Teacher;
use App\Models\Notification as NotificationModel;
use App\Domain\Notification\Enums\NotificationType;
use App\Domain\Notification\Enums\NotificationChannel;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    public function send(
        User|int $receiver,
        string $title,
        string $message,
        string|NotificationType $type = 'system',
        ?int $senderId = null,
        string $receiverType = 'admin'
    ): NotificationModel {
        $receiverId = $receiver instanceof User ? $receiver->id : $receiver;
        $branchId = auth()->user()?->branch_id;
        
        if ($receiver instanceof User && !$branchId) {
            $branchId = $receiver->branch_id;
        }

        $typeValue = $type instanceof NotificationType ? $type->value : (string)$type;

        return DB::transaction(function () use ($branchId, $senderId, $receiverId, $receiverType, $typeValue, $title, $message, $receiver) {
            $notification = NotificationModel::create([
                'branch_id' => $branchId,
                'sender_id' => $senderId ?? auth()->id(),
                'receiver_id' => $receiverId,
                'user_id' => $receiverId,
                'receiver_type' => $receiverType,
                'type' => $typeValue,
                'title' => $title,
                'message' => $message,
                'content' => $message,
                'status' => 'Unread'
            ]);

            // Dispatch Laravel native notification if receiver is User instance
            if ($receiver instanceof User) {
                try {
                    NotificationFacade::send($receiver, new GeneralNotification($title, $message, $typeValue, ['panel']));
                } catch (\Throwable $e) {
                    // Ignore mail/queue errors if running without config
                }
            }

            return $notification;
        });
    }

    public function sendToMany(
        iterable $receivers,
        string $title,
        string $message,
        string|NotificationType $type = 'system',
        ?int $senderId = null,
        string $receiverType = 'student'
    ): void {
        foreach ($receivers as $receiver) {
            $this->send($receiver, $title, $message, $type, $senderId, $receiverType);
        }
    }

    public function markAsRead(int|string $notificationId, User $user): bool
    {
        $notification = NotificationModel::where(function($q) use ($user) {
            $q->where('receiver_id', $user->id)->orWhere('user_id', $user->id);
        })->where('id', $notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    public function markAllAsRead(User $user): void
    {
        NotificationModel::where(function($q) use ($user) {
            $q->where('receiver_id', $user->id)->orWhere('user_id', $user->id);
        })->whereNull('read_at')->update([
            'read_at' => now(),
            'status' => 'Read'
        ]);
    }

    public function getUserNotifications(User $user, int $perPage = 15)
    {
        return NotificationModel::where(function($q) use ($user) {
            $q->where('receiver_id', $user->id)->orWhere('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function sendToParent(StudentGuardian $guardian, string $title, string $content, NotificationType|string $type = 'system')
    {
        if ($guardian->user) {
            $this->send($guardian->user, $title, $content, $type, auth()->id(), 'parent');
        }
    }

    public function sendToStudent(Student $student, string $title, string $content, NotificationType|string $type = 'system')
    {
        if ($student->user) {
            $this->send($student->user, $title, $content, $type, auth()->id(), 'student');
        }
    }

    public function sendToTeacher(Teacher $teacher, string $title, string $content, NotificationType|string $type = 'system')
    {
        if ($teacher->user) {
            $this->send($teacher->user, $title, $content, $type, auth()->id(), 'teacher');
        }
    }
}
