<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\User;
use App\Models\HQApiKey;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Domain\HQ\Services\HQAuditService;
use App\Domain\HQ\Services\HQAlertService;

class HQApiKeyService
{
    /**
     * Generate a new API Key for a user or tenant
     * Returns the plain text key once.
     */
    public function generateApiKey(?User $user, ?int $tenantId, string $name, ?\DateTimeInterface $expiresAt = null): string
    {
        $plainTextToken = Str::random(60);
        $tokenHash = Hash::make($plainTextToken);

        $apiKey = HQApiKey::create([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'name' => $name,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);

        app(HQAuditService::class)->logSystemAction(
            action: 'api_key_created',
            category: 'iam',
            severity: 'info',
            description: "API Key {$name} created.",
            tenantId: $tenantId
        );

        return $plainTextToken;
    }

    public function revokeApiKey(HQApiKey $apiKey): void
    {
        $apiKey->update(['is_revoked' => true]);

        app(HQAuditService::class)->logSystemAction(
            action: 'api_key_revoked',
            category: 'iam',
            severity: 'warning',
            description: "API Key {$apiKey->name} revoked.",
            tenantId: $apiKey->tenant_id
        );
        
        event(new \App\Events\ApiKeyRevoked($apiKey));
    }

    public function recordUsage(HQApiKey $apiKey): void
    {
        $apiKey->increment('usage_count');
        $apiKey->update(['last_used_at' => now()]);
        
        // Example logic for abuse detection
        if ($apiKey->usage_count > 10000) {
            app(HQAlertService::class)->createAlert(
                severity: 'warning',
                title: 'api.abuse',
                message: "API Key {$apiKey->name} exceeded usage threshold.",
                tenantId: $apiKey->tenant_id
            );
        }
    }
    
    public function validateKey(string $plainTextToken): ?HQApiKey
    {
        $keys = HQApiKey::where('is_revoked', false)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->get();
            
        foreach ($keys as $key) {
            if (Hash::check($plainTextToken, $key->token_hash)) {
                $this->recordUsage($key);
                return $key;
            }
        }
        
        return null;
    }
}
