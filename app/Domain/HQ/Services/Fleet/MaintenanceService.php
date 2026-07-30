<?php

namespace App\Domain\HQ\Services\Fleet;

use App\Models\HQMaintenanceWindow;
use App\Models\HQTenant;
use App\Models\HQInstanceGroup;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    /**
     * Process scheduled maintenance windows that should start or end.
     */
    public function processScheduledWindows()
    {
        $now = now();

        // Start scheduled windows
        $toStart = HQMaintenanceWindow::where('status', 'scheduled')
            ->where('auto_manage', true)
            ->where('starts_at', '<=', $now)
            ->get();

        foreach ($toStart as $window) {
            $this->startWindow($window);
        }

        // End active windows
        $toEnd = HQMaintenanceWindow::where('status', 'active')
            ->where('auto_manage', true)
            ->where('ends_at', '<=', $now)
            ->get();

        foreach ($toEnd as $window) {
            $this->endWindow($window);
        }
    }

    public function startWindow(HQMaintenanceWindow $window)
    {
        $window->update(['status' => 'active']);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.maintenance.started',
            category: 'fleet',
            severity: 'warning',
            description: "Maintenance window {$window->id} started for {$window->targetable_type} {$window->targetable_id}.",
            metadata: ['window_id' => $window->id]
        );

        // Here we would ideally trigger a remote command to instances to enable maintenance mode.
        // E.g., app(HQRemoteCommandService::class)->executeCommand($instances, 'artisan down');
    }

    public function endWindow(HQMaintenanceWindow $window)
    {
        $window->update(['status' => 'completed']);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'fleet.maintenance.ended',
            category: 'fleet',
            severity: 'info',
            description: "Maintenance window {$window->id} ended for {$window->targetable_type} {$window->targetable_id}.",
            metadata: ['window_id' => $window->id]
        );

        // Here we would ideally trigger a remote command to instances to disable maintenance mode.
        // E.g., app(HQRemoteCommandService::class)->executeCommand($instances, 'artisan up');
    }
}
