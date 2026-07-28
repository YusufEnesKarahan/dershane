<?php

namespace App\Domain\HQ\Services;

use App\Models\HQTenant;

class TenantService
{
    public function createTenant(array $data): HQTenant
    {
        return HQTenant::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'status' => $data['status'] ?? 'active',
        ]);
    }
}
