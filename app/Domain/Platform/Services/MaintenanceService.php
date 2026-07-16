<?php
namespace App\Domain\Platform\Services;

class MaintenanceService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function isEnabled(): bool
    {
        return $this->settingsService->get('maintenance.enabled', '0') === '1';
    }

    public function getBypassIp(): ?string
    {
        return $this->settingsService->get('maintenance.bypass_ip');
    }
}
