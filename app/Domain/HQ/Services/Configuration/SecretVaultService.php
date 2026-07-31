<?php

namespace App\Domain\HQ\Services\Configuration;

use App\Models\HQSecretVault;
use App\Models\HQSecretVersion;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;

class SecretVaultService
{
    /**
     * Store a new secret securely using Laravel Crypt.
     */
    public function store(string $name, string $key, string $plainText, ?string $rotationInterval = null, ?Carbon $expiresAt = null): HQSecretVault
    {
        $encrypted = Crypt::encryptString($plainText);

        $vault = HQSecretVault::updateOrCreate(
            ['key' => $key],
            [
                'name' => $name,
                'encrypted_value' => $encrypted,
                'rotation_interval' => $rotationInterval,
                'expires_at' => $expiresAt,
            ]
        );

        $this->createVersion($vault, $encrypted);

        return $vault;
    }

    /**
     * Retrieve and decrypt a secret.
     */
    public function get(string $key): ?string
    {
        $vault = HQSecretVault::where('key', $key)->where('is_active', true)->first();

        if (!$vault) {
            return null;
        }

        if ($vault->expires_at && $vault->expires_at->isPast()) {
            return null; // Expired
        }

        return Crypt::decryptString($vault->encrypted_value);
    }

    /**
     * Rotate an existing secret with a new value.
     */
    public function rotate(string $key, string $newPlainText): bool
    {
        $vault = HQSecretVault::where('key', $key)->first();
        if (!$vault) return false;

        $encrypted = Crypt::encryptString($newPlainText);
        $vault->update(['encrypted_value' => $encrypted]);

        $this->createVersion($vault, $encrypted);
        
        event(new \App\Events\SecretRotated($vault));

        return true;
    }

    protected function createVersion(HQSecretVault $vault, string $encryptedValue)
    {
        HQSecretVersion::create([
            'secret_vault_id' => $vault->id,
            'encrypted_value' => $encryptedValue,
            'created_by' => auth()->id() ?? 'system',
        ]);
    }
}
