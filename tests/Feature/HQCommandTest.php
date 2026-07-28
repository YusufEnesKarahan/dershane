<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HQCommand;
use App\Models\HqApiToken;
use App\Domain\Platform\Services\SignatureService;

class HQCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        config(['app.installed' => true]);
        config(['hq.enabled' => true]);
    }

    public function test_command_creation()
    {
        $command = HQCommand::create([
            'command_type' => 'test_command',
            'payload' => ['foo' => 'bar']
        ]);
        
        $command->refresh();

        $this->assertNotNull($command->command_uuid);
        $this->assertEquals('pending', $command->status);
        
        $this->assertDatabaseHas('hq_commands', [
            'id' => $command->id,
            'command_type' => 'test_command',
        ]);
    }

    public function test_allowed_command_execution()
    {
        Http::fake([
            '*/command/result' => Http::response(['success' => true], 200)
        ]);
        
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $command = HQCommand::create([
            'command_type' => 'health_check',
            'status' => 'approved'
        ]);

        $response = $this->actingAs($superAdmin)->post("/admin/platform/commands/{$command->id}/execute");
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $command->refresh();
        $this->assertEquals('executed', $command->status);
        $this->assertNotNull($command->executed_at);
        $this->assertEquals('ok', $command->result['status']);
        $this->assertTrue($command->result['database']);
    }

    public function test_unknown_command_rejection()
    {
        Http::fake([
            '*/command/result' => Http::response(['success' => true], 200)
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $command = HQCommand::create([
            'command_type' => 'malicious_command',
            'status' => 'approved'
        ]);

        $response = $this->actingAs($superAdmin)->post("/admin/platform/commands/{$command->id}/execute");
        
        $command->refresh();
        $this->assertEquals('failed', $command->status);
        $this->assertEquals('Command not allowed', $command->result['error']);
    }

    public function test_cache_clear_execution()
    {
        Http::fake([
            '*/command/result' => Http::response(['success' => true], 200)
        ]);

        Cache::put('test_key', 'test_value', 10);
        $this->assertTrue(Cache::has('test_key'));

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $command = HQCommand::create([
            'command_type' => 'cache_clear',
            'status' => 'approved'
        ]);

        $this->actingAs($superAdmin)->post("/admin/platform/commands/{$command->id}/execute");
        
        $command->refresh();
        $this->assertEquals('executed', $command->status);
        
        $this->assertFalse(Cache::has('test_key'));
    }

    public function test_system_info_execution()
    {
        Http::fake([
            '*/command/result' => Http::response(['success' => true], 200)
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $command = HQCommand::create([
            'command_type' => 'system_info',
            'status' => 'approved'
        ]);

        $this->actingAs($superAdmin)->post("/admin/platform/commands/{$command->id}/execute");
        
        $command->refresh();
        $this->assertEquals('executed', $command->status);
        $this->assertArrayHasKey('php_version', $command->result);
        $this->assertArrayHasKey('laravel_version', $command->result);
    }

    public function test_admin_access_to_commands()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/commands');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.commands.index');
        
        $normalUser = User::factory()->create(); // No super admin
        $response2 = $this->actingAs($normalUser)->get('/admin/platform/commands');
        
        $response2->assertStatus(403);
    }

    public function test_security_middleware_rejects_invalid_token()
    {
        // Setup an active token
        $activeToken = HqApiToken::create([
            'name' => 'HQ System',
            'token' => 'VALID_TOKEN_123',
            'is_active' => true,
        ]);

        $payload = ['command' => 'test'];
        
        // Use middleware manually for testing since route is not defined for HQ inbound
        $request = \Illuminate\Http\Request::create('/api/hq/command', 'POST', $payload);
        // Missing token entirely
        
        $middleware = new \App\Http\Middleware\HQCommandMiddleware(
            app(\App\Domain\Platform\Services\HQApiService::class),
            app(SignatureService::class)
        );

        $response = $middleware->handle($request, function() {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_security_middleware_rejects_invalid_signature()
    {
        $activeToken = HqApiToken::create([
            'name' => 'HQ System',
            'token' => 'VALID_TOKEN_123',
            'is_active' => true,
        ]);

        $payload = ['command' => 'test'];
        
        $request = \Illuminate\Http\Request::create('/api/hq/command', 'POST', $payload);
        $request->headers->set('Authorization', 'Bearer VALID_TOKEN_123');
        $request->headers->set('X-Signature', 'invalid_signature_hash');
        
        $middleware = new \App\Http\Middleware\HQCommandMiddleware(
            app(\App\Domain\Platform\Services\HQApiService::class),
            app(SignatureService::class)
        );

        $response = $middleware->handle($request, function() {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_security_middleware_accepts_valid_request()
    {
        $activeToken = HqApiToken::create([
            'name' => 'HQ System',
            'token' => 'VALID_TOKEN_123',
            'is_active' => true,
        ]);

        $payload = ['command' => 'test'];
        $signature = app(SignatureService::class)->generate($payload, 'VALID_TOKEN_123');
        
        $request = \Illuminate\Http\Request::create('/api/hq/command', 'POST', $payload);
        $request->headers->set('Authorization', 'Bearer VALID_TOKEN_123');
        $request->headers->set('X-Signature', $signature);
        
        $middleware = new \App\Http\Middleware\HQCommandMiddleware(
            app(\App\Domain\Platform\Services\HQApiService::class),
            app(SignatureService::class)
        );

        $response = $middleware->handle($request, function() {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }
}
