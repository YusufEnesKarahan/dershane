<?php

namespace App\Observers;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class NotificationObserver
{
    public function creating(Notification $notification)
    {
        if (isset($notification->data) && is_array($notification->data)) {
            if (isset($notification->data['title']) && empty($notification->title)) {
                $notification->title = $notification->data['title'];
            }
            if (isset($notification->data['content']) && empty($notification->message)) {
                $notification->message = $notification->data['content'];
            }
        }
    }

    public function saved(Notification $notification)
    {
        Cache::forget('communication.analytics.summary');
    }
}
