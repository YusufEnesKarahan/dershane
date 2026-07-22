<?php

namespace App\DTOs\Communication;

class SendAnnouncementDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public ?int $announcement_group_id = null,
        public bool $is_published = true
    ) {}
}
