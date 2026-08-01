<?php

namespace App\Domain\Identity\Services;

use App\Core\Services\AuditService;
use Illuminate\Support\Facades\Log;

class IdentityAuditService
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function logSecurityEvent($tenant, $user, string $action, array $context = [])
    {
        Log::info("IdentityAuditService [{$action}]: User {$user->id} on Tenant " . ($tenant ? $tenant->id : 'Global'));
        
        if ($tenant) {
            $this->auditService->logEvent($tenant, $action, 'identity', array_merge([
                'user_id' => $user->id,
            ], $context));
        }
    }
}
