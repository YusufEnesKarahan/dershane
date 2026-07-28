<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HqApiToken;
use App\Domain\Platform\Services\HQApiService;
use App\Domain\Platform\Services\HQIntegrationService;
use App\Http\Middleware\HQApiMiddleware;
use Illuminate\Support\Facades\Route;

class HQApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_generate_token()
    {
        $service = app(HQApiService::class);
        $token = $service->generateToken('Test token', 30);

        $this->assertNotNull($token->token);
        $this->assertEquals(64, strlen($token->token));
        $this->assertTrue($token->is_active);
        $this->assertEquals('Test token', $token->name);
        $this->assertNotNull($token->expires_at);
    }

    public function test_validate_token()
    {
        $service = app(HQApiService::class);
        $token = $service->generateToken('Test token', 30);

        $this->assertTrue($service->validateToken($token->token));
    }

    public function test_invalid_token()
    {
        $service = app(HQApiService::class);
        
        // Non-existent token
        $this->assertFalse($service->validateToken('non_existent_token_string_value_here'));

        // Expired token
        $expiredToken = HqApiToken::create([
            'token' => \Illuminate\Support\Str::random(64),
            'name' => 'Expired Token',
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);
        $this->assertFalse($service->validateToken($expiredToken->token));

        // Revoked (inactive) token
        $inactiveToken = HqApiToken::create([
            'token' => \Illuminate\Support\Str::random(64),
            'name' => 'Inactive Token',
            'expires_at' => now()->addDays(5),
            'is_active' => false,
        ]);
        $this->assertFalse($service->validateToken($inactiveToken->token));
    }

    public function test_health_payload()
    {
        $service = app(HQApiService::class);
        $payload = $service->healthPayload();

        $this->assertArrayHasKey('installation_uuid', $payload);
        $this->assertArrayHasKey('system_uuid', $payload);
        $this->assertArrayHasKey('version', $payload);
        $this->assertArrayHasKey('license', $payload);
        $this->assertArrayHasKey('features', $payload);
        $this->assertArrayHasKey('active_users', $payload);
        $this->assertArrayHasKey('timestamp', $payload);
    }

    public function test_admin_api_page()
    {
        config(['app.installed' => true]);

        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::where('name', 'Super Admin')->first()->id);
        
        $response = $this->actingAs($superAdmin)->get('/admin/platform/api');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.platform.api.index');
        $response->assertViewHas('token');
    }

    public function test_middleware_blocks_invalid_tokens()
    {
        Route::get('/test-hq-api-route', function() {
            return response()->json(['message' => 'Passed']);
        })->middleware(HQApiMiddleware::class);

        // No token
        $response = $this->getJson('/test-hq-api-route');
        $response->assertStatus(401);

        // Invalid token
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid_token'])->getJson('/test-hq-api-route');
        $response->assertStatus(401);

        // Valid token
        $service = app(HQApiService::class);
        $token = $service->generateToken('Valid token', 10);
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token->token])->getJson('/test-hq-api-route');
        $response->assertStatus(200);
    }
}
