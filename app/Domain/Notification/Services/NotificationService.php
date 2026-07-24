<?php

namespace App\Domain\Notification\Services;

use App\Core\Repositories\Interfaces\NotificationRepositoryInterface;
use App\DTOs\Notification\CreateNotificationDTO;
use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function __construct(private readonly NotificationRepositoryInterface $notifications, private readonly NotificationChannelService $channels) {}

    public function create(CreateNotificationDTO $dto): Notification
    {
        return DB::transaction(function () use ($dto): Notification {
            $notification = $this->notifications->create([
                'user_id' => $dto->userId, 'type' => $dto->type, 'title' => $dto->title,
                'message' => $dto->message, 'content' => $dto->message, 'data' => $dto->data,
                'channel' => $dto->channel, 'priority' => $dto->priority, 'status' => 'Unread',
            ]);
            return $notification;
        });
    }

    public function send(Notification $notification, ?string $channel = null): Notification
    {
        $channels = $channel ? [$channel] : [$notification->channel];
        $preference = NotificationPreference::firstOrCreate(['user_id' => $notification->user_id]);
        foreach ($channels as $selected) {
            if (($selected === 'panel' && !$preference->panel_enabled) || ($selected === 'email' && !$preference->email_enabled) || ($selected === 'sms' && !$preference->sms_enabled)) continue;
            $this->channels->send($notification->loadMissing('user'), $selected);
        }
        return $notification->refresh();
    }

    public function markRead(Notification $notification): Notification { return $this->notifications->markRead($notification); }
}
