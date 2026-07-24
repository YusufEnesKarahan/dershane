<?php
namespace App\Domain\Notification\Actions;
use App\DTOs\Notification\NotificationTemplateDTO;
use App\Domain\Notification\Services\NotificationTemplateService;
use App\Models\NotificationTemplate;
class CreateTemplate { public function __construct(private readonly NotificationTemplateService $service) {} public function execute(NotificationTemplateDTO $dto): NotificationTemplate { return $this->service->create($dto); } }
