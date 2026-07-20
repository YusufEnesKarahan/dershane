<?php

namespace Tests\Feature;

use App\Services\HqService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class HqIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'hq.url' => 'http://127.0.0.1:8000',
            'hq.site_uuid' => (string) Str::uuid(),
            'hq.api_secret' => 'hq_sk_test_secret_key_1234567890',
        ]);
    }

    public function test_it_can_register_site_successfully_with_hq()
    {
        Http::fake([
            '*/api/v1/hq/register' => Http::response([
                'success' => true,
                'site_id' => 1,
                'site_uuid' => config('hq.site_uuid'),
                'api_secret' => 'hq_sk_new_secret_key_12345',
                'license_status' => 'active',
                'sync_interval' => 900,
            ], 201),
        ]);

        $service = app(HqService::class);
        $result = $service->register();

        $this->assertTrue($result['success']);
        $this->assertEquals('active', Cache::get('hq_license_status'));
    }

    public function test_it_can_sync_telemetry_and_receive_pending_commands()
    {
        Http::fake([
            '*/api/v1/hq/sync' => Http::response([
                'success' => true,
                'license_status' => 'active',
                'pending_commands' => [
                    [
                        'id' => 10,
                        'command_type' => 'clear_cache',
                        'payload' => [],
                    ],
                ],
                'synced_at' => now()->toIso8601String(),
            ], 200),
            '*/api/v1/hq/command-result' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $service = app(HqService::class);
        $result = $service->sync();

        $this->assertTrue($result['success']);
        $this->assertEquals('active', $result['license_status']);
        $this->assertCount(1, $result['executed_commands']);
        $this->assertEquals('executed', $result['executed_commands'][0]['status']);
    }

    public function test_it_handles_invalid_api_key_rejection_gracefully()
    {
        Http::fake([
            '*/api/v1/hq/sync' => Http::response([
                'success' => false,
                'message' => 'Unauthorized: Invalid API secret key.',
            ], 401),
        ]);

        $service = app(HqService::class);
        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('401', $result['message']);
    }

    public function test_it_handles_invalid_signature_rejection_gracefully()
    {
        Http::fake([
            '*/api/v1/hq/sync' => Http::response([
                'success' => false,
                'message' => 'Unauthorized: Invalid request signature.',
            ], 401),
        ]);

        $service = app(HqService::class);
        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('401', $result['message']);
    }

    public function test_it_handles_expired_timestamp_rejection_gracefully()
    {
        Http::fake([
            '*/api/v1/hq/sync' => Http::response([
                'success' => false,
                'message' => 'Unauthorized: Request timestamp expired.',
            ], 401),
        ]);

        $service = app(HqService::class);
        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('401', $result['message']);
    }

    public function test_it_executes_pending_command_and_dispatches_result_callback()
    {
        Http::fake([
            '*/api/v1/hq/command-result' => Http::response([
                'success' => true,
                'message' => 'Command result updated successfully.',
            ], 200),
        ]);

        $service = app(HqService::class);
        $result = $service->sendCommandResult(15, 'executed', 'Cache cleared');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:8000/api/v1/hq/command-result' &&
                $request->hasHeader('X-HQ-API-KEY') &&
                $request->hasHeader('X-HQ-TIMESTAMP') &&
                $request->hasHeader('X-HQ-SIGNATURE');
        });
    }

    public function test_it_handles_hq_offline_server_exceptions_without_crashing()
    {
        Http::fake([
            '*/api/v1/hq/sync' => Http::response(null, 500),
        ]);

        $service = app(HqService::class);
        $result = $service->sync();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('500', $result['message']);
    }

    public function test_it_retries_failed_requests_according_to_config()
    {
        Http::fake([
            '*/api/v1/hq/sync' => Http::sequence()
                ->pushStatus(503)
                ->pushStatus(503)
                ->push([
                    'success' => true,
                    'license_status' => 'active',
                    'pending_commands' => [],
                ], 200),
        ]);

        $service = app(HqService::class);
        $result = $service->sync();

        $this->assertTrue($result['success']);
    }
}
