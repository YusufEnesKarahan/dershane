<?php

namespace App\Domain\Parent\Actions;

use App\DTOs\Parent\ParentNotificationDTO;
use App\Domain\Parent\Services\ParentNotificationService;
use App\Models\ParentNotification;

class SendParentNotification
{
    public function __construct(protected ParentNotificationService $service) {}

    public function execute(ParentNotificationDTO $dto): ParentNotification
    {
        return $this->service->send($dto);
    }
}
