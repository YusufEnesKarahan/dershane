<?php

namespace App\Domain\Platform\Services;

class SignatureService
{
    /**
     * Generate HMAC SHA256 signature for the given payload and secret.
     */
    public function generate(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}
