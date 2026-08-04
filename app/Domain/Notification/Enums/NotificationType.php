<?php

namespace App\Domain\Notification\Enums;

enum NotificationType: string
{
    case ANNOUNCEMENT = 'announcement';
    case SYSTEM = 'system';
    case ABSENCE = 'absence';
    case PAYMENT = 'payment';
    case HOMEWORK_SUBMITTED = 'homework_submitted';
    case HOMEWORK_GRADED = 'homework_graded';
    
    public function label(): string
    {
        return match($this) {
            self::ANNOUNCEMENT => 'Duyuru',
            self::SYSTEM => 'Sistem Bildirimi',
            self::ABSENCE => 'Devamsızlık',
            self::PAYMENT => 'Ödeme',
            self::HOMEWORK_SUBMITTED => 'Ödev Teslimi',
            self::HOMEWORK_GRADED => 'Ödev Notu',
        };
    }
}
