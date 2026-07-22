<?php

namespace App\Domain\Parent\Services;

use App\Models\ParentAccessLog;
use App\Models\ParentStudent;
use App\Models\ParentNotification;

class ParentAnalyticsService
{
    public function getSummary(int $parentId): array
    {
        $loginsCount = ParentAccessLog::where('parent_id', $parentId)->count();
        $linkedStudentsCount = ParentStudent::where('parent_id', $parentId)->count();
        $unreadNotifications = ParentNotification::where('parent_id', $parentId)->where('is_read', false)->count();

        return [
            'logins_count' => $loginsCount,
            'linked_students_count' => $linkedStudentsCount,
            'unread_notifications_count' => $unreadNotifications,
        ];
    }
}
