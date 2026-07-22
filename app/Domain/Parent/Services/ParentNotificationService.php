<?php

namespace App\Domain\Parent\Services;

use App\DTOs\Parent\ParentNotificationDTO;
use App\Models\ParentNotification;
use App\Models\ParentDevice;
use Illuminate\Support\Collection;

class ParentNotificationService
{
    public function getNotifications(int $parentId): Collection
    {
        return ParentNotification::where('parent_id', $parentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function send(ParentNotificationDTO $dto): ParentNotification
    {
        return ParentNotification::create([
            'parent_id' => $dto->parent_id,
            'title' => $dto->title,
            'content' => $dto->content,
            'is_read' => false,
        ]);
    }
}
