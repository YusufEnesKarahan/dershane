<?php

namespace App\Domain\Communication\Services;

use App\DTOs\Communication\SendAnnouncementDTO;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Core\Repositories\Interfaces\AnnouncementRepositoryInterface;

class AnnouncementService
{
    public function __construct(protected AnnouncementRepositoryInterface $repository) {}

    public function publish(SendAnnouncementDTO $dto): Announcement
    {
        return $this->repository->create([
            'title' => $dto->title,
            'content' => $dto->content,
            'announcement_group_id' => $dto->announcement_group_id,
            'is_published' => $dto->is_published,
            'published_at' => now(),
        ]);
    }

    public function markAsRead(int $announcementId, int $userId): AnnouncementRead
    {
        return AnnouncementRead::updateOrCreate(
            [
                'announcement_id' => $announcementId,
                'user_id' => $userId,
            ],
            [
                'read_at' => now(),
            ]
        );
    }
}
