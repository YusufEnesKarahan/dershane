<?php

namespace App\DTOs\Notification;

class CreateNotificationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type = 'system',
        public readonly string $channel = 'panel',
        public readonly string $priority = 'normal',
        public readonly array $data = [],
    ) {}
}
