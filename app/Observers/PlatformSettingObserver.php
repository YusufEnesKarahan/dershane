<?php
namespace App\Observers;

use App\Models\PlatformSetting;
use App\Domain\Platform\Services\PlatformSettingsService;
use App\Domain\Platform\Services\ThemeService;

class PlatformSettingObserver
{
    public function __construct(
        protected PlatformSettingsService $settingsService,
        protected ThemeService $themeService
    ) {}

    public function updated(PlatformSetting $setting)
    {
        $this->settingsService->clearCache($setting->key);

        // If theme settings change, automatically rebuild the theme CSS file!
        if (str_starts_with($setting->key, 'theme.')) {
            $this->themeService->compileThemeCss();
        }
    }
}
