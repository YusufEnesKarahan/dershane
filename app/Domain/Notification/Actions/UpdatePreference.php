<?php
namespace App\Domain\Notification\Actions;
use App\DTOs\Notification\NotificationPreferenceDTO;
use App\Models\NotificationPreference;
class UpdatePreference { public function execute(NotificationPreferenceDTO $dto): NotificationPreference { return NotificationPreference::updateOrCreate(['user_id' => $dto->userId], ['email_enabled' => $dto->emailEnabled, 'panel_enabled' => $dto->panelEnabled, 'sms_enabled' => $dto->smsEnabled]); } }
