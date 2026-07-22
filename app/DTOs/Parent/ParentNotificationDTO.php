<?php

namespace App\DTOs\Parent;

class ParentNotificationDTO
{
    public function __construct(
        public int $parent_id,
        public string $title,
        public string $content
    ) {}
}
