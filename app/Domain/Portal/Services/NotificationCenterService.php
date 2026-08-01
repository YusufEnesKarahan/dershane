<?php

namespace App\Domain\Portal\Services;

use App\Models\Institution;
use App\Models\PortalNotification;
use App\Events\NotificationRead;

class NotificationCenterService
{
    public function getNotifications(Institution $tenant)
    {
        return PortalNotification::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadNotifications(Institution $tenant)
    {
        return PortalNotification::where('tenant_id', $tenant->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead(PortalNotification $notification)
    {
        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
            event(new NotificationRead($notification));
        }

        return $notification;
    }

    public function sendNotification(Institution $tenant, string $title, string $message, string $type = 'info')
    {
        // This can be triggered synchronously or via SendPortalNotificationJob
        return PortalNotification::create([
            'tenant_id' => $tenant->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }
}
