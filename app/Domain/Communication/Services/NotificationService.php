<?php

namespace App\Domain\Communication\Services;

use App\DTOs\Communication\CreateNotificationDTO;
use App\Models\Notification;
use App\Models\NotificationLog;
use App\Core\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationService
{
    public function __construct(protected NotificationRepositoryInterface $repository) {}

    public function createNotification(CreateNotificationDTO $dto): Notification
    {
        $notification = $this->repository->create(['user_id' => $dto->user_id, 'title' => $dto->title, 'message' => $dto->content, 'content' => $dto->content, 'type' => $dto->type, 'channel' => strtolower($dto->type) === 'system' ? 'panel' : strtolower($dto->type), 'status' => $dto->status]);

        // Auto-create log entry for system channel
        NotificationLog::create([
            'notification_id' => $notification->id,
            'recipient' => 'User ID: ' . $dto->user_id,
            'channel' => $dto->type,
            'provider' => 'InternalSystem',
            'status' => 'Sent',
            'sent_at' => now(),
        ]);

        return $notification;
    }
}
