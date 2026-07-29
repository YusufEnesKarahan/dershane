<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Models\HQCentralCommand;
use App\Models\User;
use App\Domain\HQ\Enums\HQCommandType;
use App\Domain\HQ\Services\HQRemoteCommandService;
use App\Domain\System\Commands\RemoteCommandExecutor;
use App\Domain\System\Commands\CommandRegistry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RemoteCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['hq.api.token' => 'test-secret-token']);
        config(['hq.api.secret' => 'test-secret-key']);
    }

    protected function getSignedHeaders(array $payload = []): array
    {
        $timestamp = now()->timestamp;
        $signature = hash_hmac('sha256', json_encode($payload) . $timestamp, config('hq.api.secret'));

        return [
            'Authorization' => 'Bearer ' . config('hq.api.token'),
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => $timestamp,
            'Accept' => 'application/json',
        ];
    }

    // 1. Service: Command Creation & Whitelist Validation (via Enum)
    public function test_service_can_dispatch_valid_commands_and_reject_invalid_enum()
    {
        $tenant = HQTenant::create(['name' => 'T', 'slug' => 't']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'u1', 'system_name' => 'S1', 'system_version' => '1.0', 'status' => 'online']);
        $service = app(HQRemoteCommandService::class);

        $command = $service->dispatchCommand($instance, HQCommandType::PING, ['data' => 1], 10);
        
        $this->assertInstanceOf(HQCentralCommand::class, $command);
        $this->assertEquals('ping', $command->command_type);
        $this->assertEquals(10, $command->priority);
        $this->assertEquals('pending', $command->status);
        $this->assertEquals(['data' => 1], $command->payload);
        
        // Assert invalid enum throws Error in PHP 8.1+ or cannot be passed due to typing
        $this->expectException(\TypeError::class);
        $service->dispatchCommand($instance, 'INVALID_COMMAND', [], 0);
    }

    // 2. Service: Priority Sorting & Expiration
    public function test_pending_commands_are_sorted_by_priority_and_expire_correctly()
    {
        $tenant = HQTenant::create(['name' => 'T', 'slug' => 't2']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'u2', 'system_name' => 'S1', 'system_version' => '1.0', 'status' => 'online']);
        $service = app(HQRemoteCommandService::class);

        // Expired command
        $service->dispatchCommand($instance, HQCommandType::PING, [], 100, null, now()->subMinute());
        
        // Valid commands with different priorities
        $cmdLow = $service->dispatchCommand($instance, HQCommandType::SYNC_LICENSE, [], 1);
        $cmdHigh = $service->dispatchCommand($instance, HQCommandType::CLEAR_CACHE, [], 99);
        $cmdMed = $service->dispatchCommand($instance, HQCommandType::PING, [], 50);

        $pending = $service->getPendingCommands($instance);

        $this->assertCount(3, $pending);
        
        // Check sorting
        $this->assertEquals($cmdHigh->id, $pending[0]->id);
        $this->assertEquals($cmdMed->id, $pending[1]->id);
        $this->assertEquals($cmdLow->id, $pending[2]->id);

        // Check expiration
        $expired = HQCentralCommand::where('status', 'failed')->first();
        $this->assertNotNull($expired);
        $this->assertEquals('Command expired before it could be picked up.', $expired->error_message);
    }

    // 3. Service: Bulk operations (Transaction)
    public function test_service_bulk_dispatch_creates_commands_for_all_tenant_instances()
    {
        $tenant = HQTenant::create(['name' => 'T', 'slug' => 't3']);
        HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'u31', 'system_name' => 'S1', 'system_version' => '1.0', 'status' => 'online']);
        HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'u32', 'system_name' => 'S2', 'system_version' => '1.0', 'status' => 'online']);
        HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'u33', 'system_name' => 'S3', 'system_version' => '1.0', 'status' => 'online']);
        
        $service = app(HQRemoteCommandService::class);
        $commands = $service->dispatchToTenant($tenant, HQCommandType::PING, [], 5);

        $this->assertCount(3, $commands);
        $this->assertEquals(3, HQCentralCommand::count());
        $this->assertEquals(5, HQCentralCommand::first()->priority);
    }

    // 4. API: Pull commands endpoint (Authorization & Results)
    public function test_erp_can_pull_pending_commands_securely()
    {
        $tenant = HQTenant::create(['name' => 'T', 'slug' => 't4']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'test-uuid', 'system_name' => 'S1', 'system_version' => '1.0', 'status' => 'online']);
        $service = app(HQRemoteCommandService::class);
        $service->dispatchCommand($instance, HQCommandType::PING, [], 0);

        // Unauthorized (No headers)
        $response = $this->getJson("/api/hq/commands?system_uuid=test-uuid");
        $response->assertStatus(401);

        // Authorized
        $response = $this->getJson("/api/hq/commands?system_uuid=test-uuid", $this->getSignedHeaders([]));
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertTrue(isset($data['commands']));
        $this->assertCount(1, $data['commands']);
        $this->assertEquals('ping', $data['commands'][0]['type']);

        // Check status updated to sent
        $this->assertEquals('sent', HQCentralCommand::first()->status);
    }

    // 5. API: Submit Result Endpoint (Success & Retry logic)
    public function test_erp_submits_successful_result_and_updates_hq()
    {
        $tenant = HQTenant::create(['name' => 'T', 'slug' => 't5']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'test-uuid2', 'system_name' => 'S1', 'system_version' => '1.0', 'status' => 'online']);
        $service = app(HQRemoteCommandService::class);
        $command = $service->dispatchCommand($instance, HQCommandType::PING, [], 0);
        
        $payload = ['system_uuid' => 'test-uuid2', 'success' => true, 'message' => 'pong'];
        $response = $this->postJson("/api/hq/commands/{$command->id}/result", $payload, $this->getSignedHeaders($payload));
        
        $response->assertStatus(200);
        
        $command->refresh();
        $this->assertEquals('completed', $command->status);
        $this->assertNotNull($command->executed_at);
        $this->assertTrue($command->response['success']);
    }

    public function test_erp_submits_failed_result_and_triggers_retry()
    {
        $tenant = HQTenant::create(['name' => 'T', 'slug' => 't6']);
        $instance = HQSystemInstance::create(['tenant_id' => $tenant->id, 'system_uuid' => 'test-uuid3', 'system_name' => 'S1', 'system_version' => '1.0', 'status' => 'online']);
        $service = app(HQRemoteCommandService::class);
        $command = $service->dispatchCommand($instance, HQCommandType::PING, [], 0);
        
        $payload = ['system_uuid' => 'test-uuid3', 'success' => false, 'message' => 'failed temp'];
        
        // Retry 1
        $this->postJson("/api/hq/commands/{$command->id}/result", $payload, $this->getSignedHeaders($payload))->assertStatus(200);
        $command->refresh();
        $this->assertEquals('pending', $command->status); // Put back to queue
        $this->assertEquals(1, $command->retry_count);
        
        // Retry 2
        $this->postJson("/api/hq/commands/{$command->id}/result", $payload, $this->getSignedHeaders($payload))->assertStatus(200);
        $command->refresh();
        $this->assertEquals('pending', $command->status);
        $this->assertEquals(2, $command->retry_count);
        
        // Retry 3 (Max)
        $this->postJson("/api/hq/commands/{$command->id}/result", $payload, $this->getSignedHeaders($payload))->assertStatus(200);
        $command->refresh();
        $this->assertEquals('failed', $command->status); // Max retry hit
        $this->assertEquals(3, $command->retry_count);
        $this->assertEquals('failed temp', $command->error_message);
    }

    // 6. ERP Side: CommandRegistry & Handler execution
    public function test_registry_resolves_only_whitelisted_commands()
    {
        $handler = CommandRegistry::resolve('ping');
        $this->assertInstanceOf(\App\Domain\System\Commands\Handlers\PingHandler::class, $handler);

        $handler = CommandRegistry::resolve('clear_cache');
        $this->assertInstanceOf(\App\Domain\System\Commands\Handlers\ClearCacheHandler::class, $handler);

        $handler = CommandRegistry::resolve('invalid_hack');
        $this->assertNull($handler);
    }

    public function test_clear_cache_handler_flushes_cache_without_exec()
    {
        Cache::put('test_key', 'test_value', 60);
        $this->assertEquals('test_value', Cache::get('test_key'));

        $handler = CommandRegistry::resolve('clear_cache');
        $result = $handler->handle([]);

        $this->assertTrue($result['success']);
        $this->assertNull(Cache::get('test_key'));
    }

    // 7. ERP Side: Executor pulls, processes, and pushes
    public function test_remote_command_executor_end_to_end()
    {
        // Mock HQHttpService to simulate pulling commands and pushing results
        Http::fake([
            '*/api/hq/commands' => Http::response([
                'status' => 'success',
                'commands' => [
                    ['id' => 999, 'type' => 'ping', 'payload' => []]
                ]
            ], 200),
            '*/api/hq/commands/999/result' => Http::response(['status' => 'success'], 200),
        ]);

        $executor = app(RemoteCommandExecutor::class);
        $result = $executor->processPendingCommands();

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(1, $result['processed']);
        $this->assertEquals(0, $result['failed']);
        
        // Verify HTTP calls were made
        Http::assertSentCount(2);
    }
}
