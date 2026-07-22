<?php

namespace App\Domain\Communication\Services;

use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\Announcement;
use App\Models\AnnouncementRead;

class NotificationAnalyticsService
{
    public function getSummary(): array
    {
        $totalNotifications = Notification::count();
        $totalAnnouncements = Announcement::count();
        $totalReads = AnnouncementRead::count();

        $sentCount = NotificationLog::where('status', 'Sent')->count();
        $failedCount = NotificationLog::where('status', 'Failed')->count();
        $deliveryRate = ($sentCount + $failedCount) > 0 ? round(($sentCount / ($sentCount + $failedCount)) * 100, 1) : 100.0;

        return [
            'total_notifications' => $totalNotifications,
            'total_announcements' => $totalAnnouncements,
            'total_reads' => $totalReads,
            'delivery_rate' => $deliveryRate,
        ];
    }
}
