<?php
namespace App\Domain\Platform\Services;

class SeoDefaultsService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function getSeoDefaults(): array
    {
        return [
            'meta_title' => $this->settingsService->get('seo.meta_title', 'Dershane Platformu'),
            'meta_description' => $this->settingsService->get('seo.meta_description', 'Dershane online eğitim platformu'),
            'meta_keywords' => $this->settingsService->get('seo.meta_keywords'),
            'robots' => $this->settingsService->get('seo.robots', 'index, follow'),
        ];
    }
}
