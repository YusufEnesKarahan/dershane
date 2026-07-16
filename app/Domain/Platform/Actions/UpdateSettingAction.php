<?php
namespace App\Domain\Platform\Actions;

use App\DTOs\Platform\UpdateSettingDTO;
use App\Domain\Platform\Services\PlatformSettingsService;

class UpdateSettingAction
{
    public function __construct(protected PlatformSettingsService $service) {}

    public function execute(UpdateSettingDTO $dto): void
    {
        foreach ($dto->settings as $key => $val) {
            $this->service->set($key, $val);
        }
    }
}
