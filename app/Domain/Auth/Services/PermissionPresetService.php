<?php
namespace App\Domain\Auth\Services;

class PermissionPresetService
{
    public function getPreset(string $preset): array
    {
        return match ($preset) {
            'CRUD' => ['view', 'create', 'update', 'delete'],
            'Read Only' => ['view'],
            'Management' => ['view', 'create', 'update'],
            'Full Access' => ['*'],
            'Export' => ['export'],
            'Import' => ['import'],
            default => []
        };
    }
}
