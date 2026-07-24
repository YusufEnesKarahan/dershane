<?php

namespace App\DTOs\Notification;

class NotificationTemplateDTO
{
    public function __construct(public readonly string $name, public readonly string $slug, public readonly string $titleTemplate, public readonly string $bodyTemplate, public readonly string $channel = 'panel', public readonly bool $isActive = true) {}
}
