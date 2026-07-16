<?php
namespace App\Domain\Platform\Services;

class BrandService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function getBranding(): array
    {
        return [
            'company_name' => $this->settingsService->get('brand.company_name', 'Dershane'),
            'short_name' => $this->settingsService->get('brand.short_name', 'Dershane'),
            'legal_name' => $this->settingsService->get('brand.legal_name', 'Dershane A.Ş.'),
            'logo' => $this->settingsService->get('brand.logo'),
            'dark_logo' => $this->settingsService->get('brand.dark_logo'),
            'favicon' => $this->settingsService->get('brand.favicon'),
        ];
    }
}
