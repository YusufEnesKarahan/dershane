<?php

namespace App\Domain\Platform\Services;

use App\Models\HqApiToken;
use Illuminate\Support\Str;
use App\Models\User;

class HQApiService
{
    public function __construct(
        protected HQIntegrationService $hqIntegrationService
    ) {}

    /**
     * Generate a new secure API token for HQ.
     */
    public function generateToken(string $name = 'HQ central connection', ?int $expiresInDays = 365): HqApiToken
    {
        // First deactivate any existing active tokens
        HqApiToken::where('is_active', true)->update(['is_active' => false]);

        return HqApiToken::create([
            'token' => Str::random(64),
            'name' => $name,
            'expires_at' => $expiresInDays ? now()->addDays($expiresInDays) : null,
            'is_active' => true,
        ]);
    }

    /**
     * Revoke a token.
     */
    public function revokeToken(string $token): bool
    {
        $apiToken = HqApiToken::where('token', $token)->first();

        if ($apiToken) {
            return $apiToken->update(['is_active' => false]);
        }

        return false;
    }

    /**
     * Validate a token.
     */
    public function validateToken(string $token): bool
    {
        $apiToken = HqApiToken::where('token', $token)
            ->where('is_active', true)
            ->first();

        if (!$apiToken) {
            return false;
        }

        if ($apiToken->expires_at && $apiToken->expires_at->isPast()) {
            return false;
        }

        $apiToken->update(['last_used_at' => now()]);

        return true;
    }

    /**
     * Get the active token.
     */
    public function getActiveToken(): ?HqApiToken
    {
        return HqApiToken::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    /**
     * Get basic ping response.
     */
    public function pingResponse(): array
    {
        return ['status' => 'pong'];
    }

    /**
     * Get health payload.
     */
    public function healthPayload(): array
    {
        $identity = $this->hqIntegrationService->getInstanceInformation();
        $license = $this->hqIntegrationService->getLicenseStatus();
        $features = $this->hqIntegrationService->getEnabledFeatures();
        $activeUsers = User::count();

        return [
            'installation_uuid' => $identity->installation_uuid,
            'system_uuid' => $identity->uuid,
            'version' => $this->hqIntegrationService->getSystemVersion(),
            'license' => $license['status'] ?? 'Unknown',
            'features' => $features,
            'active_users' => $activeUsers,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Get system payload.
     */
    public function systemPayload(): array
    {
        return $this->healthPayload();
    }
}
