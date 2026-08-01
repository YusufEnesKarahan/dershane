<?php

namespace App\Domain\Portal\Services;

use App\Models\Institution;
use App\Models\PortalApiKey;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Events\PortalApiKeyCreated;
use App\Events\PortalApiKeyRevoked;

class ApiKeyService
{
    public function createKey(Institution $tenant, string $name, $userId = null): array
    {
        $plainTextKey = 'pk_' . Str::random(40);
        
        $apiKey = PortalApiKey::create([
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'name' => $name,
            'key_hash' => Hash::make($plainTextKey),
            // We do not store the plain key. We only show it once.
        ]);

        app(\App\Core\Services\AuditService::class)->logSystemAction(
            action: 'portal_api_key_created',
            category: 'portal',
            severity: 'info',
            description: "API Key '{$name}' created for tenant {$tenant->id}.",
            metadata: ['api_key_id' => $apiKey->id]
        );

        event(new PortalApiKeyCreated($apiKey));

        return [
            'api_key' => $apiKey,
            'plain_text_key' => $plainTextKey
        ];
    }

    public function revokeKey(PortalApiKey $apiKey)
    {
        $apiKey->update(['status' => 'revoked']);

        app(\App\Core\Services\AuditService::class)->logSystemAction(
            action: 'portal_api_key_revoked',
            category: 'portal',
            severity: 'warning',
            description: "API Key '{$apiKey->name}' revoked for tenant {$apiKey->tenant_id}.",
            metadata: ['api_key_id' => $apiKey->id]
        );

        event(new PortalApiKeyRevoked($apiKey));

        return $apiKey;
    }

    public function rotateKey(PortalApiKey $oldKey): array
    {
        $this->revokeKey($oldKey);
        return $this->createKey($oldKey->tenant, $oldKey->name . ' (Rotated)', $oldKey->user_id);
    }
}
