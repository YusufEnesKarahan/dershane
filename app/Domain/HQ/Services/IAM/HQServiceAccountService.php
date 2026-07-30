<?php

namespace App\Domain\HQ\Services\IAM;

use App\Models\HQServiceAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Domain\HQ\Services\HQAuditService;

class HQServiceAccountService
{
    public function createServiceAccount(int $tenantId, string $name, string $description = null): array
    {
        $plainToken = Str::random(64);
        $tokenHash = Hash::make($plainToken);

        $account = HQServiceAccount::create([
            'tenant_id' => $tenantId,
            'name' => $name,
            'description' => $description,
            'token_hash' => $tokenHash,
        ]);

        app(HQAuditService::class)->logSystemAction(
            action: 'service_account_created',
            category: 'iam',
            severity: 'info',
            description: "Service account {$name} created.",
            tenantId: $tenantId
        );

        return [
            'account' => $account,
            'plain_token' => $plainToken
        ];
    }

    public function disableServiceAccount(HQServiceAccount $account): void
    {
        $account->update(['is_active' => false]);
        
        app(HQAuditService::class)->logSystemAction(
            action: 'service_account_disabled',
            category: 'iam',
            severity: 'warning',
            description: "Service account {$account->name} disabled.",
            tenantId: $account->tenant_id
        );
    }
    
    public function validateToken(string $plainToken): ?HQServiceAccount
    {
        $accounts = HQServiceAccount::where('is_active', true)->get();
        foreach ($accounts as $account) {
            if (Hash::check($plainToken, $account->token_hash)) {
                return $account;
            }
        }
        return null;
    }
}
