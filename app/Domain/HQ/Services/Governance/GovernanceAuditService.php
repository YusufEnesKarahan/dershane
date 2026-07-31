<?php

namespace App\Domain\HQ\Services\Governance;

use App\Models\HQGovernanceAudit;

class GovernanceAuditService
{
    public function log(string $action, ?string $entityType = null, ?int $entityId = null, ?array $changes = null, ?int $tenantId = null, ?int $userId = null): void
    {
        HQGovernanceAudit::create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => $userId ?? auth()->id(),
            'tenant_id' => $tenantId,
            'changes' => $changes,
        ]);
    }
}
