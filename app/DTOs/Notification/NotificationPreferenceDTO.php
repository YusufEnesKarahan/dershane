<?php

namespace App\DTOs\Notification;

class NotificationPreferenceDTO
{
    public function __construct(public readonly int $userId, public readonly bool $emailEnabled = true, public readonly bool $panelEnabled = true, public readonly bool $smsEnabled = false) {}
}
