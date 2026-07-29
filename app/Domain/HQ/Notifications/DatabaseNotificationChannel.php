<?php

namespace App\Domain\HQ\Notifications;

use App\Domain\HQ\Contracts\NotificationChannelInterface;
use App\Models\HQAlert;
use App\Models\HQNotificationLog;

class DatabaseNotificationChannel implements NotificationChannelInterface
{
    public function send(HQAlert $alert): bool
    {
        try {
            // In a real application, you might use Laravel's Database Notifications here.
            // For now, our HQNotificationLog acts as a simple database log for the notification.
            HQNotificationLog::create([
                'alert_id' => $alert->id,
                'channel' => 'database',
                'recipient' => 'hq_admins',
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            HQNotificationLog::create([
                'alert_id' => $alert->id,
                'channel' => 'database',
                'recipient' => 'hq_admins',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
