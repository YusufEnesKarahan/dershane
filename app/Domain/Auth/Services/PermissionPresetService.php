<?php
namespace App\Domain\Auth\Services;

class PermissionPresetService
{
    public function getPreset(string $preset): array
    {
        return config("permission-presets.presets.{$preset}", []);
    }
}
