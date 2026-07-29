<?php

namespace App\Domain\System\Services;

use App\Models\ConfigurationCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ConfigurationSynchronizationService
{
    /**
     * Pull configuration from HQ and sync locally.
     */
    public function syncFromHQ(): bool
    {
        try {
            $token = config('hq.api.token');
            $secret = config('hq.api.secret');
            $url = config('hq.url') . '/api/hq/configuration/sync';
            $systemUuid = config('hq.system_uuid');

            if (!$token || !$secret || !$url || !$systemUuid) {
                Log::warning('Configuration synchronization aborted: HQ configuration missing.');
                return false;
            }

            $timestamp = time();
            $payload = [
                'system_uuid' => $systemUuid,
                'environment' => config('app.env'),
            ];

            $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, $secret);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-HQ-Signature' => $signature,
                'X-HQ-Timestamp' => (string) $timestamp,
                'Accept' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['configurations']) && is_array($data['configurations'])) {
                    $this->cacheLocally($data['configurations'], $data['version'] ?? 1);
                    return true;
                }
            } else {
                Log::error('HQ Configuration Sync failed: ' . $response->body());
            }
        } catch (Exception $e) {
            Log::error('HQ Configuration Sync exception: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Store retrieved config into the local DB cache.
     */
    protected function cacheLocally(array $configurations, int $version)
    {
        // For absolute consistency, we might delete existing ones that are not in the new payload.
        $receivedKeys = array_column($configurations, 'key');
        
        ConfigurationCache::whereNotIn('key', $receivedKeys)->delete();

        foreach ($configurations as $config) {
            ConfigurationCache::updateOrCreate(
                ['key' => $config['key']],
                [
                    'value' => $config['value'],
                    'type' => $config['type'] ?? 'string',
                    'version' => $version,
                    'last_synced_at' => now(),
                ]
            );
        }
    }

    /**
     * Clear all cached configurations.
     */
    public function clearCache()
    {
        ConfigurationCache::truncate();
    }
}
