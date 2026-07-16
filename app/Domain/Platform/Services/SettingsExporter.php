<?php
namespace App\Domain\Platform\Services;

use App\Core\Repositories\Interfaces\PlatformSettingRepositoryInterface;
use Illuminate\Support\Facades\Crypt;

class SettingsExporter
{
    public function __construct(protected PlatformSettingRepositoryInterface $repository) {}

    public function exportJson(bool $encrypt = false): string
    {
        $settings = $this->repository->all()->map(fn($s) => [
            'key' => $s->key,
            'value' => $s->value,
            'is_encrypted' => $s->is_encrypted,
        ])->toArray();

        $json = json_encode($settings, JSON_PRETTY_PRINT);
        
        if ($encrypt) {
            return Crypt::encryptString($json);
        }

        return $json;
    }
}
