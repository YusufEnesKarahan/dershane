<?php

namespace App\Domain\Communication\Actions;

use App\Domain\Communication\Services\AnnouncementService;
use App\Models\AnnouncementRead;

class MarkAnnouncementRead
{
    public function __construct(protected AnnouncementService $service) {}

    public function execute(int $announcementId, int $userId): AnnouncementRead
    {
        return $this->service->markAsRead($announcementId, $userId);
    }
}
