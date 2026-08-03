<?php

namespace App\Domain\Notification\Enums;

enum NotificationChannel: string
{
    case DATABASE = 'database';
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
}
