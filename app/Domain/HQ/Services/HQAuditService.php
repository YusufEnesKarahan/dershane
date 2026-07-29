<?php

namespace App\Domain\HQ\Services;

use App\Models\HQAuditLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class HQAuditService
{
    /**
     * General log method.
     */
    public static function log(
        string $action,
        string $category,
        string $severity = 'info',
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $tenantId = null,
        ?int $systemInstanceId = null,
        ?array $metadata = null
    ): HQAuditLog {
        return HQAuditLog::create([
            'user_id' => Auth::id(),
            'tenant_id' => $tenantId,
            'system_instance_id' => $systemInstanceId,
            'action' => $action,
            'category' => $category,
            'severity' => $severity,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
        ]);
    }

    public static function logUserAction(string $action, string $description = null, array $metadata = null): HQAuditLog
    {
        return self::log(
            action: $action,
            category: 'user',
            severity: 'info',
            description: $description,
            metadata: $metadata
        );
    }

    public static function logSystemAction(
        string $action, 
        string $category = 'system', 
        string $severity = 'info', 
        string $description = null,
        ?int $tenantId = null,
        ?int $systemInstanceId = null,
        array $metadata = null
    ): HQAuditLog {
        return self::log(
            action: $action,
            category: $category,
            severity: $severity,
            description: $description,
            tenantId: $tenantId,
            systemInstanceId: $systemInstanceId,
            metadata: $metadata
        );
    }

    public static function logSecurityEvent(
        string $action,
        string $severity = 'danger',
        string $description = null,
        array $metadata = null
    ): HQAuditLog {
        return self::log(
            action: $action,
            category: 'security',
            severity: $severity,
            description: $description,
            metadata: $metadata
        );
    }

    public function getTimeline(array $filters = [], int $perPage = 20)
    {
        $query = HQAuditLog::with(['user', 'tenant', 'systemInstance'])->latest('created_at');

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }
        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }
        if (!empty($filters['system_instance_id'])) {
            $query->where('system_instance_id', $filters['system_instance_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['date_start'])) {
            $query->where('created_at', '>=', $filters['date_start']);
        }
        if (!empty($filters['date_end'])) {
            $query->where('created_at', '<=', $filters['date_end']);
        }

        return $query->paginate($perPage);
    }

    public function getStatistics()
    {
        $last24Hours = now()->subDay();

        return [
            'recent_events_count' => HQAuditLog::where('created_at', '>=', $last24Hours)->count(),
            'critical_events_count' => HQAuditLog::where('created_at', '>=', $last24Hours)
                                                ->where('severity', 'critical')->count(),
            'failed_operations_count' => HQAuditLog::where('created_at', '>=', $last24Hours)
                                                ->where('action', 'like', '%failed%')->count(),
            'recent_activities' => HQAuditLog::with(['user', 'systemInstance'])
                                            ->latest('created_at')
                                            ->take(5)
                                            ->get(),
        ];
    }
}
