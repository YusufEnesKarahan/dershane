<?php

namespace App\Observers;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;

class NotificationObserver
{
    public function saved(Notification $notification)
    {
        Cache::forget('communication.analytics.summary');
    }
}
