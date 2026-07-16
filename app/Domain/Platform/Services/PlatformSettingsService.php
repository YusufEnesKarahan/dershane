<?php
namespace App\Domain\Platform\Services;

use App\Core\Repositories\Interfaces\PlatformSettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class PlatformSettingsService
{
    public function __construct(protected PlatformSettingRepositoryInterface $repository) {}

    public function get(string $key, $default = null): ?string
    {
        return Cache::remember('platform.settings.' . $key, 3600, function () use ($key, $default) {
            $setting = $this->repository->findByKey($key);
            return $setting ? $setting->value : $default;
        });
    }

    public function set(string $key, ?string $value): void
    {
        $setting = $this->repository->findByKey($key);
        if ($setting) {
            $this->repository->update($setting, ['value' => $value]);
        }
        Cache::forget('platform.settings.' . $key);
    }

    public function clearCache(string $key): void
    {
        Cache::forget('platform.settings.' . $key);
    }
}
