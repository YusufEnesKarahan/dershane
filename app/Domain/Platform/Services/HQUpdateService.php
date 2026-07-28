<?php

namespace App\Domain\Platform\Services;

use App\Models\HQUpdate;
use App\Models\HQUpdateLog;
use Illuminate\Support\Facades\Log;

class HQUpdateService
{
    public function __construct(
        protected HQHttpService $hqHttpService,
        protected HQIntegrationService $hqIntegrationService
    ) {}

    public function currentVersion(): string
    {
        // Currently returns the static app version or a mocked version for testing
        // You could also read it from a config or composer.json
        return config('app.version', '1.0.0');
    }

    public function checkAvailable(): ?array
    {
        $payload = [
            'system_uuid' => $this->hqIntegrationService->getInstanceInformation()->uuid ?? 'UNKNOWN',
            'version' => $this->currentVersion(),
            'channel' => config('hq.updates.channel', 'stable')
        ];

        try {
            $response = $this->hqHttpService->checkUpdates($payload);
            if (isset($response['update_available']) && $response['update_available']) {
                $this->registerUpdate($response['update_data']);
                return $response['update_data'];
            }
        } catch (\Exception $e) {
            Log::error('HQ Update check failed: ' . $e->getMessage());
        }
        
        return null;
    }

    public function registerUpdate(array $data): HQUpdate
    {
        $update = HQUpdate::updateOrCreate(
            ['version' => $data['version']],
            [
                'channel' => $data['channel'] ?? 'stable',
                'package_url' => $data['package_url'] ?? null,
                'checksum' => $data['checksum'] ?? null,
                'status' => 'available',
                'released_at' => $data['released_at'] ?? now(),
                'metadata' => $data['metadata'] ?? [],
            ]
        );

        $this->logAction($update->id, 'registered', 'success', 'Update metadata registered from HQ.');

        return $update;
    }

    public function markInstalled(HQUpdate $update): void
    {
        $update->update([
            'status' => 'installed',
            'installed_at' => now(),
        ]);

        $this->logAction($update->id, 'installed', 'success', 'Update marked as installed manually or via mock.');
    }

    public function getLatest(): ?HQUpdate
    {
        return HQUpdate::orderBy('created_at', 'desc')->first();
    }

    protected function logAction(int $updateId, string $action, string $status, string $message): void
    {
        HQUpdateLog::create([
            'update_id' => $updateId,
            'action' => $action,
            'status' => $status,
            'message' => $message,
        ]);
    }
}
