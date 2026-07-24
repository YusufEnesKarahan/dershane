<?php
namespace App\Domain\Notification\Actions;
use App\DTOs\Notification\CreateNotificationDTO;
use App\Domain\Notification\Services\NotificationService;
use App\Models\Notification;
class CreateNotification { public function __construct(private readonly NotificationService $service) {} public function execute(CreateNotificationDTO $dto): Notification { return $this->service->create($dto); } }
