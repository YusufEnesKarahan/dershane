<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\User;
use App\Models\HQMfaSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use App\Domain\HQ\Services\HQAuditService;

class MfaService
{
    public function enableMfa(User $user): array
    {
        // Simple TOTP placeholder logic
        $secret = Str::random(32);
        
        $recoveryCodes = [];
        for ($i=0; $i<8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }

        HQMfaSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_enabled' => true,
                'secret' => Crypt::encryptString($secret),
                'recovery_codes' => $recoveryCodes, // in real app, these should be hashed
            ]
        );

        app(HQAuditService::class)->logUserAction(
            action: 'mfa_enabled',
            description: "MFA enabled for user {$user->id}"
        );

        return [
            'secret' => $secret,
            'recovery_codes' => $recoveryCodes
        ];
    }

    public function disableMfa(User $user): void
    {
        HQMfaSetting::where('user_id', $user->id)->update([
            'is_enabled' => false,
            'secret' => null,
            'recovery_codes' => null
        ]);
        
        app(HQAuditService::class)->logUserAction(
            action: 'mfa_disabled',
            description: "MFA disabled for user {$user->id}"
        );
    }

    public function verifyTotp(User $user, string $code): bool
    {
        $setting = HQMfaSetting::where('user_id', $user->id)->where('is_enabled', true)->first();
        if (!$setting) return false;
        
        // Use a library like pragmarx/google2fa to verify the code
        // For simulation, we assume '123456' is the valid code
        return $code === '123456';
    }
}
