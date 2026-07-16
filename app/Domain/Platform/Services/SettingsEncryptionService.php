<?php
namespace App\Domain\Platform\Services;

use Illuminate\Support\Facades\Crypt;

class SettingsEncryptionService
{
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function decrypt(string $payload): string
    {
        return Crypt::decryptString($payload);
    }
}
