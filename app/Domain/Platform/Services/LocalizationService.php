<?php
namespace App\Domain\Platform\Services;

class LocalizationService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function getDefaultLocale(): string
    {
        return $this->settingsService->get('localization.default_locale', 'tr');
    }
}
