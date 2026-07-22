<?php

namespace App\Domain\Communication\Actions;

use App\DTOs\Communication\SendAnnouncementDTO;
use App\Domain\Communication\Services\AnnouncementService;
use App\Models\Announcement;

class SendAnnouncement
{
    public function __construct(protected AnnouncementService $service) {}

    public function execute(SendAnnouncementDTO $dto): Announcement
    {
        return $this->service->publish($dto);
    }
}
