<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HqService
{
    protected string $hqUrl;
    protected ?string $siteUuid;
    protected ?string $apiSecret;
    protected string $siteType;
    protected string $siteName;
    protected int $requestTimeout;
    protected int $connectTimeout;
    protected int $retryCount;
    protected int $retryDelay;

    public function __construct()
    {
        $this->hqUrl = rtrim(config('hq.url', 'http://127.0.0.1:8000'), '/');
        $this->siteUuid = config('hq.site_uuid');
        $this->apiSecret = config('hq.api_secret');
        $this->siteType = config('hq.site_type', 'dershane');
        $this->siteName = config('hq.site_name', 'Dershane ERP');
        $this->requestTimeout = (int) config('hq.request_timeout', 10);
        $this->connectTimeout = (int) config('hq.connect_timeout', 5);
        $this->retryCount = (int) config('hq.retry_count', 3);
        $this->retryDelay = (int) config('hq.retry_delay', 200);
    }

    /**
     * Registers this site instance with central HQ Panel.
     */
    public function register(): array
    {
        Log::channel('hq')->info('REGISTER: Starting registration attempt', ['hq_url' => $this->hqUrl]);

        $uuid = $this->siteUuid ?: (string) Str::uuid();
        $domain = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?? 'localhost';

        $payload = [
            'uuid' => $uuid,
            'name' => $this->siteName,
            'site_type' => $this->siteType,
            'primary_domain' => $domain,
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'app_version' => '1.0.0',
        ];

        try {
            $response = Http::timeout($this->requestTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryCount, $this->retryDelay, function ($exception) {
                    Log::channel('hq')->warning('REGISTER: Retrying registration request', ['error' => $exception->getMessage()]);
                    return true;
                }, throw: false)
                ->post("{$this->hqUrl}/api/v1/hq/register", $payload);

            if ($response->successful()) {
                $data = $response->json();

                $newSecret = $data['api_secret'] ?? '';
                $newUuid = $data['site_uuid'] ?? $uuid;

                $this->updateEnv([
                    'HQ_SITE_UUID' => $newUuid,
                    'HQ_API_SECRET' => $newSecret,
                ]);

                $this->updateLicense($data['license_status'] ?? 'active');

                Log::channel('hq')->info('REGISTER: Registration successful', ['uuid' => $newUuid]);

                return [
                    'success' => true,
                    'message' => 'Site HQ Paneline başarıyla kaydedildi.',
                    'data' => $data,
                ];
            }

            Log::channel('hq')->error('REGISTER: Registration rejected by HQ', ['status' => $response->status(), 'body' => $response->body()]);

            return [
                'success' => false,
                'message' => 'HQ Kayıt başarısız (HTTP ' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::channel('hq')->error('REGISTER: Exception encountered', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'HQ Sunucusuna erişilemedi: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Synchronizes heartbeat, telemetry, and pending commands with HQ Panel using HMAC authentication.
     */
    public function sync(): array
    {
        Log::channel('hq')->info('SYNC: Starting periodic synchronization');

        $uuid = config('hq.site_uuid');
        $secret = config('hq.api_secret');

        if (! $uuid || ! $secret) {
            Log::channel('hq')->warning('SYNC: Missing credentials in configuration');
            return [
                'success' => false,
                'message' => 'HQ Kimlik bilgileri eksik. Lütfen önce php artisan hq:register komutunu çalıştırın.',
            ];
        }

        $payload = [
            'site_uuid' => $uuid,
            'telemetry' => $this->collectTelemetry(),
        ];

        $rawBody = json_encode($payload);
        $headers = $this->generateHmacHeaders($rawBody, $secret);

        try {
            $response = Http::timeout($this->requestTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryCount, $this->retryDelay, function ($exception) {
                    Log::channel('hq')->warning('SYNC: Retrying sync request', ['error' => $exception->getMessage()]);
                    return true;
                }, throw: false)
                ->withHeaders($headers)
                ->withBody($rawBody, 'application/json')
                ->post("{$this->hqUrl}/api/v1/hq/sync");

            if ($response->successful()) {
                $data = $response->json();

                $licenseStatus = $data['license_status'] ?? 'active';
                $this->updateLicense($licenseStatus);

                $executedCommands = [];
                if (! empty($data['pending_commands']) && is_array($data['pending_commands'])) {
                    $executedCommands = $this->executePendingCommands($data['pending_commands']);
                }

                Log::channel('hq')->info('SYNC: Synchronization completed successfully', [
                    'license_status' => $licenseStatus,
                    'executed_commands_count' => count($executedCommands),
                ]);

                return [
                    'success' => true,
                    'license_status' => $licenseStatus,
                    'executed_commands' => $executedCommands,
                    'synced_at' => $data['synced_at'] ?? now()->toIso8601String(),
                ];
            }

            Log::channel('hq')->error('SYNC: Synchronization rejected by HQ', ['status' => $response->status(), 'body' => $response->body()]);

            return [
                'success' => false,
                'message' => 'HQ Sync başarısız (HTTP ' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::channel('hq')->error('SYNC: Exception encountered', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'HQ Sunucusuna erişilemedi: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Executes pending commands received from HQ and dispatches result callbacks.
     */
    public function executePendingCommands(array $commands): array
    {
        Log::channel('hq')->info('COMMAND: Executing pending commands', ['count' => count($commands)]);

        $executedResults = [];

        foreach ($commands as $cmd) {
            $type = $cmd['command_type'] ?? '';
            $cmdId = (int) ($cmd['id'] ?? 0);

            Log::channel('hq')->info("COMMAND: Executing command #{$cmdId} ({$type})");

            $status = 'executed';
            $output = null;

            try {
                switch ($type) {
                    case 'clear_cache':
                        Artisan::call('cache:clear');
                        Artisan::call('config:clear');
                        $output = 'Cache and config cleared successfully.';
                        break;

                    case 'maintenance':
                        if (! app()->isDownForMaintenance()) {
                            Artisan::call('down');
                            $output = 'Application enabled maintenance mode.';
                        } else {
                            $output = 'Application was already in maintenance mode.';
                        }
                        break;

                    case 'license_sync':
                        Cache::forget('hq_license_status');
                        $output = 'License cache cleared for sync.';
                        break;

                    default:
                        $status = 'failed';
                        $output = "Unknown command type: {$type}";
                        break;
                }
            } catch (\Throwable $e) {
                $status = 'failed';
                $output = 'Execution error: ' . $e->getMessage();
                Log::channel('hq')->error("COMMAND: Command #{$cmdId} failed", ['error' => $e->getMessage()]);
            }

            if ($cmdId > 0) {
                $this->sendCommandResult($cmdId, $status, $output);
            }

            $executedResults[] = [
                'id' => $cmdId,
                'type' => $type,
                'status' => $status,
                'output' => $output,
            ];
        }

        return $executedResults;
    }

    /**
     * Updates local license status cache.
     */
    public function updateLicense(string $status): void
    {
        Cache::put('hq_license_status', $status, 10);
        Log::channel('hq')->info("INFO: Local license status updated to '{$status}'");
    }

    /**
     * Dispatches command execution result callback to HQ Panel.
     */
    public function sendCommandResult(int $commandId, string $status, mixed $output): bool
    {
        $secret = config('hq.api_secret');
        if (! $secret) {
            return false;
        }

        $payload = [
            'command_id' => $commandId,
            'status' => $status,
            'output' => $output,
            'executed_at' => now()->toIso8601String(),
        ];

        $rawBody = json_encode($payload);
        $headers = $this->generateHmacHeaders($rawBody, $secret);

        try {
            $response = Http::timeout($this->requestTimeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryCount, $this->retryDelay, throw: false)
                ->withHeaders($headers)
                ->withBody($rawBody, 'application/json')
                ->post("{$this->hqUrl}/api/v1/hq/command-result");

            Log::channel('hq')->info("COMMAND: Result callback sent for command #{$commandId}", ['status' => $status]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::channel('hq')->error("COMMAND: Failed to send result callback for command #{$commandId}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generates HMAC-SHA256 signature headers.
     */
    protected function generateHmacHeaders(string $rawBody, string $secret): array
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', $rawBody . $timestamp, $secret);

        return [
            'X-HQ-API-KEY' => $secret,
            'X-HQ-TIMESTAMP' => (string) $timestamp,
            'X-HQ-SIGNATURE' => $signature,
        ];
    }

    /**
     * Collects telemetry data from environment.
     */
    protected function collectTelemetry(): array
    {
        $dbStatus = 'connected';
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'error';
        }

        $diskUsage = 0;
        try {
            $total = disk_total_space(base_path());
            $free = disk_free_space(base_path());
            if ($total > 0) {
                $diskUsage = (int) round((($total - $free) / $total) * 100);
            }
        } catch (\Throwable $e) {
            $diskUsage = 0;
        }

        $activeUsers = 0;
        try {
            $activeUsers = User::count();
        } catch (\Throwable $e) {
            $activeUsers = 0;
        }

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_status' => $dbStatus,
            'disk_usage_percentage' => $diskUsage,
            'active_users_count' => $activeUsers,
            'extra' => [
                'environment' => app()->environment(),
            ],
        ];
    }

    /**
     * Safely updates .env file.
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (str_contains($content, "{$key}=")) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }
}
