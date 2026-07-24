<?php

namespace App\DTOs\Notification;

class SendNotificationDTO
{
    public function __construct(public readonly int $notificationId, public readonly ?string $channel = null) {}
}
