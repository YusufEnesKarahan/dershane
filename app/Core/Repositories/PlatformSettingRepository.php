<?php
namespace App\Core\Repositories;

use App\Models\PlatformSetting;
use App\Core\Repositories\Interfaces\PlatformSettingRepositoryInterface;
use Illuminate\Support\Collection;

class PlatformSettingRepository implements PlatformSettingRepositoryInterface
{
    public function all(): Collection
    {
        return PlatformSetting::orderBy('sort_order')->get();
    }

    public function findByKey(string $key): ?PlatformSetting
    {
        return PlatformSetting::where('key', $key)->first();
    }

    public function create(array $data): PlatformSetting
    {
        return PlatformSetting::create($data);
    }

    public function update(PlatformSetting $setting, array $data): PlatformSetting
    {
        $setting->update($data);
        return $setting;
    }
}
