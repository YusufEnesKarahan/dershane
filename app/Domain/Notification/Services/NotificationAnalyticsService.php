<?php

namespace App\Domain\Notification\Services;

use App\Models\Notification;
use App\Models\NotificationLog;

class NotificationAnalyticsService
{
    public function summary(): array
    {
        $total = Notification::count();
        $read = Notification::whereNotNull('read_at')->count();
        return [
            'total_notifications' => $total,
            'read_rate' => $total ? round(($read / $total) * 100, 1) : 0.0,
            'channel_distribution' => Notification::selectRaw('channel, count(*) as total')->groupBy('channel')->pluck('total', 'channel'),
            'daily_sends' => NotificationLog::selectRaw('DATE(COALESCE(sent_at, created_at)) as date, count(*) as total')->where('status', 'Sent')->groupBy('date')->orderBy('date')->limit(14)->get(),
            'delivery_rate' => $this->deliveryRate(),
        ];
    }
    private function deliveryRate(): float { $attempted = NotificationLog::count(); return $attempted ? round(NotificationLog::where('status', 'Sent')->count() / $attempted * 100, 1) : 100.0; }
}
