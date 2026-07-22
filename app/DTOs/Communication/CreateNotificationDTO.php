<?php

namespace App\DTOs\Communication;

class CreateNotificationDTO
{
    public function __construct(
        public int $user_id,
        public string $title,
        public string $content,
        public string $type = 'System',
        public string $status = 'Unread'
    ) {}
}
