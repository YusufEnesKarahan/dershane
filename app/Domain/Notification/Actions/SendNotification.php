<?php
namespace App\Domain\Notification\Actions;
use App\DTOs\Notification\SendNotificationDTO;
use App\Domain\Notification\Services\NotificationService;
use App\Models\Notification;
class SendNotification { public function __construct(private readonly NotificationService $service) {} public function execute(SendNotificationDTO $dto): Notification { return $this->service->send(Notification::query()->findOrFail($dto->notificationId), $dto->channel); } }
