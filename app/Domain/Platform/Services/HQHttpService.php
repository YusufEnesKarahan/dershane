<?php

namespace App\Domain\Platform\Services;

use App\Models\HQSyncLog;
use Illuminate\Support\Facades\Http;
use Exception;

class HQHttpService
{
    public function __construct(
        protected HQApiService $hqApiService,
        protected HQIntegrationService $hqIntegrationService,
        protected SignatureService $signatureService
    ) {}

    /**
     * Send a request to HQ via Laravel HTTP Client.
     */
    public function send(string $method, string $endpoint, array $payload = []): array
    {
        if (!config('hq.enabled')) {
            return ['success' => false, 'message' => 'HQ communication is disabled.'];
        }

        $baseUrl = rtrim(config('hq.base_url'), '/');
        $url = $baseUrl . '/' . ltrim($endpoint, '/');
        $activeToken = $this->hqApiService->getActiveToken();
        
        $token = $activeToken ? $activeToken->token : 'NO_TOKEN_AVAILABLE';
        
        $identity = $this->hqIntegrationService->getInstanceInformation();
        $systemUuid = $identity ? $identity->uuid : 'UNKNOWN';
        $installationUuid = $identity ? $identity->installation_uuid : 'UNKNOWN';
        $version = $this->hqIntegrationService->getSystemVersion();
        $licenseStatus = $this->hqIntegrationService->getLicenseStatus();
        $license = $licenseStatus['status'] ?? 'Unknown';

        $signature = $this->signatureService->generate($payload, $token);

        $start = microtime(true);
        $status = null;
        $responsePayload = [];
        $isSuccess = false;

        try {
            $client = Http::withHeaders([
                'X-System-UUID' => $systemUuid,
                'X-Installation-UUID' => $installationUuid,
                'X-System-Version' => $version,
                'X-License' => $license,
                'X-Signature' => $signature,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withToken($token)
              ->timeout(config('hq.timeout', 10))
              ->retry(2, 100);

            if (!config('hq.verify_ssl', true)) {
                $client = $client->withoutVerifying();
            }

            if (strtolower($method) === 'get') {
                $response = $client->get($url, $payload);
            } else {
                $response = $client->post($url, $payload);
            }

            $status = $response->status();
            $responsePayload = $response->json() ?? [];
            $isSuccess = $response->successful();

        } catch (Exception $e) {
            $status = 500;
            $responsePayload = ['error' => $e->getMessage()];
        }

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        HQSyncLog::create([
            'event_type' => $endpoint,
            'request_url' => $url,
            'request_method' => strtoupper($method),
            'request_payload' => $payload,
            'response_status' => $status,
            'response_payload' => $responsePayload,
            'duration_ms' => $durationMs,
            'success' => $isSuccess,
            'created_at' => now(),
        ]);

        return array_merge(['success' => $isSuccess, 'status' => $status], (array) $responsePayload);
    }

    public function ping(): array
    {
        return $this->send('get', 'ping');
    }

    public function health(): array
    {
        $payload = $this->hqApiService->healthPayload();
        return $this->send('post', 'health', $payload);
    }

    public function register(): array
    {
        $payload = $this->hqApiService->systemPayload();
        return $this->send('post', 'register', $payload);
    }

    public function sync(): array
    {
        // For this sprint, just manual trigger sending health data as sync mock
        $payload = $this->hqApiService->healthPayload();
        $payload['manual_sync'] = true;
        return $this->send('post', 'sync', $payload);
    }

    public function sendCommandResult(string $commandUuid, array $result): array
    {
        $payload = [
            'command_uuid' => $commandUuid,
            'result' => $result,
        ];
        return $this->send('post', 'command/result', $payload);
    }

    public function sendTelemetry(array $payload): array
    {
        return $this->send('post', 'telemetry', $payload);
    }

    public function checkUpdates(array $payload): array
    {
        return $this->send('post', 'updates/check', $payload);
    }

    public function validateLicense(array $payload): array
    {
        return $this->send('post', 'hq/license/validate', $payload);
    }
}
