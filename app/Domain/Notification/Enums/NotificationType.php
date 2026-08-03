<?php

namespace App\Domain\Notification\Enums;

enum NotificationType: string
{
    case ANNOUNCEMENT = 'announcement';
    case SYSTEM = 'system';
    case ABSENCE = 'absence';
    case PAYMENT = 'payment';
    
    public function label(): string
    {
        return match($this) {
            self::ANNOUNCEMENT => 'Duyuru',
            self::SYSTEM => 'Sistem Bildirimi',
            self::ABSENCE => 'Devamsızlık',
            self::PAYMENT => 'Ödeme',
        };
    }
}
