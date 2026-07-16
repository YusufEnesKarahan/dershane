<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\PlatformSetting;
use Illuminate\Support\Collection;

interface PlatformSettingRepositoryInterface
{
    public function all(): Collection;
    public function findByKey(string $key): ?PlatformSetting;
    public function create(array $data): PlatformSetting;
    public function update(PlatformSetting $setting, array $data): PlatformSetting;
}
