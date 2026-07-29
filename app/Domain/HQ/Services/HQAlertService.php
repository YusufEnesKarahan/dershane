<?php

namespace App\Domain\HQ\Services;

use App\Models\HQAlert;
use App\Models\HQAlertRule;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use App\Domain\HQ\Notifications\DatabaseNotificationChannel;
use App\Domain\HQ\Notifications\MailNotificationChannel;
use Illuminate\Support\Facades\DB;

class HQAlertService
{
    /**
     * Create a new alert and send notifications.
     */
    public function createAlert(
        string $severity,
        string $title,
        string $message,
        ?int $ruleId = null,
        ?int $tenantId = null,
        ?int $systemInstanceId = null,
        array $metadata = []
    ): HQAlert {
        return DB::transaction(function () use (
            $severity,
            $title,
            $message,
            $ruleId,
            $tenantId,
            $systemInstanceId,
            $metadata
        ) {
            $alert = HQAlert::create([
                'rule_id' => $ruleId,
                'tenant_id' => $tenantId,
                'system_instance_id' => $systemInstanceId,
                'title' => $title,
                'message' => $message,
                'severity' => $severity,
                'status' => 'open',
                'metadata' => $metadata,
            ]);

            $this->sendNotifications($alert);

            return $alert;
        });
    }

    /**
     * Mark an alert as acknowledged.
     */
    public function acknowledgeAlert(HQAlert $alert): bool
    {
        if ($alert->status === 'resolved') {
            return false;
        }

        return $alert->update(['status' => 'acknowledged']);
    }

    /**
     * Mark an alert as resolved.
     */
    public function resolveAlert(HQAlert $alert): bool
    {
        return $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    /**
     * Get active alerts.
     */
    public function getActiveAlerts(int $limit = 50)
    {
        return HQAlert::whereIn('status', ['open', 'acknowledged'])
            ->with(['tenant', 'systemInstance', 'rule'])
            ->latest('triggered_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get statistics for dashboard.
     */
    public function getStatistics(): array
    {
        return [
            'open_count' => HQAlert::where('status', 'open')->count(),
            'acknowledged_count' => HQAlert::where('status', 'acknowledged')->count(),
            'critical_count' => HQAlert::whereIn('status', ['open', 'acknowledged'])->where('severity', 'critical')->count(),
            'resolved_today' => HQAlert::where('status', 'resolved')->whereDate('resolved_at', today())->count(),
        ];
    }

    /**
     * Send notifications for an alert using all configured channels.
     */
    protected function sendNotifications(HQAlert $alert): void
    {
        $channels = [
            new DatabaseNotificationChannel(),
            new MailNotificationChannel(),
        ];

        foreach ($channels as $channel) {
            $channel->send($alert);
        }
    }
}
