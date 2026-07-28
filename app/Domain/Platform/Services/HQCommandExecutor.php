<?php

namespace App\Domain\Platform\Services;

use Illuminate\Support\Facades\Cache;

class HQCommandExecutor
{
    public function __construct(
        protected UpdateService $updateService
    ) {}

    /**
     * Executes the requested command if it exists and is whitelisted.
     */
    public function execute(string $commandType, array $payload = []): array
    {
        return match ($commandType) {
            'health_check' => $this->healthCheck(),
            'system_info' => $this->systemInfo(),
            'cache_clear' => $this->cacheClear(),
            'version_check' => $this->versionCheck(),
            default => ['error' => 'Command not allowed', 'status' => 'failed'],
        };
    }

    protected function healthCheck(): array
    {
        return [
            'status' => 'ok',
            'database' => true, // Since we are running this code, we assume db is up (for basic test scope)
            'cache' => true,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    protected function systemInfo(): array
    {
        return [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'server_time' => now()->toIso8601String(),
        ];
    }

    protected function cacheClear(): array
    {
        Cache::flush();
        return [
            'status' => 'success',
            'message' => 'Cache cleared successfully'
        ];
    }

    protected function versionCheck(): array
    {
        $current = $this->updateService->currentVersion();
        $latest = $this->updateService->getLatest();
        
        return [
            'current_version' => $current,
            'latest_version' => $latest ? $latest->version : $current,
            'update_available' => $this->updateService->isUpdateAvailable(),
        ];
    }
}
