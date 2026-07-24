<?php
namespace App\Domain\Notification\Actions;
use App\Domain\Notification\Services\NotificationService;
use App\Models\Notification;
class MarkNotificationRead { public function __construct(private readonly NotificationService $service) {} public function execute(Notification $notification): Notification { return $this->service->markRead($notification); } }
