<?php
namespace App\Domain\Auth\Services;

use App\Domain\Auth\Dictionaries\PermissionDictionary;

class PermissionGroupService
{
    public function getGroupedPermissions(): array
    {
        $grouped = [];
        foreach (PermissionDictionary::DICTIONARY as $name => $meta) {
            $group = $meta['group'];
            $grouped[$group][] = [
                'name' => $name,
                'label' => $meta['label']
            ];
        }
        return $grouped;
    }
}
