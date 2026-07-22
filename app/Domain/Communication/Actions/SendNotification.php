<?php

namespace App\Domain\Communication\Actions;

use App\DTOs\Communication\CreateNotificationDTO;
use App\Domain\Communication\Services\NotificationService;
use App\Models\Notification;

class SendNotification
{
    public function __construct(protected NotificationService $service) {}

    public function execute(CreateNotificationDTO $dto): Notification
    {
        return $this->service->createNotification($dto);
    }
}
