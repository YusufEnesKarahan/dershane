<?php
namespace App\Domain\Platform\Services;

use App\Core\Repositories\Interfaces\PlatformSettingRepositoryInterface;
use Illuminate\Support\Facades\Crypt;

class SettingsImporter
{
    public function __construct(protected PlatformSettingRepositoryInterface $repository) {}

    public function importJson(string $data, bool $isEncrypted = false): bool
    {
        try {
            $json = $isEncrypted ? Crypt::decryptString($data) : $data;
            $settings = json_decode($json, true);

            if (!is_array($settings)) {
                return false;
            }

            foreach ($settings as $item) {
                $setting = $this->repository->findByKey($item['key']);
                if ($setting) {
                    $this->repository->update($setting, ['value' => $item['value']]);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
