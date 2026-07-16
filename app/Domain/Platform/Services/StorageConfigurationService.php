<?php
namespace App\Domain\Platform\Services;

class StorageConfigurationService
{
    public function __construct(protected PlatformSettingsService $settingsService) {}

    public function testStorageDisk(): bool
    {
        $disk = $this->settingsService->get('storage.default_disk', 'public');
        return in_array($disk, ['public', 'local', 's3'], true);
    }
}
