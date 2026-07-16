<?php
namespace App\Domain\Platform\Actions;

use App\Core\Repositories\Interfaces\PlatformSettingRepositoryInterface;

class ResetSettingsAction
{
    public function __construct(protected PlatformSettingRepositoryInterface $repository) {}

    public function execute(): void
    {
        // Resets settings to empty value or default state
        foreach ($this->repository->all() as $setting) {
            $this->repository->update($setting, ['value' => null]);
        }
    }
}
