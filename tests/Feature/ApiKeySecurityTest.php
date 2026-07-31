<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\PortalApiKey;
use App\Domain\Portal\Services\ApiKeyService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;

class ApiKeySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_key_is_created_and_hashed()
    {
        Event::fake([\App\Events\PortalApiKeyCreated::class]);
        
        $tenant = HQTenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $service = app(ApiKeyService::class);
        
        $result = $service->createKey($tenant, 'Production Key');
        
        $this->assertArrayHasKey('api_key', $result);
        $this->assertArrayHasKey('plain_text_key', $result);
        
        $apiKey = $result['api_key'];
        
        $this->assertEquals('Production Key', $apiKey->name);
        $this->assertTrue(Hash::check($result['plain_text_key'], $apiKey->key_hash));
    }

    public function test_api_key_can_be_revoked()
    {
        Event::fake([\App\Events\PortalApiKeyRevoked::class]);

        $tenant = HQTenant::create(['name' => 'Acme Corp', 'slug' => 'acme-corp', 'uuid' => \Illuminate\Support\Str::uuid()]);
        $apiKey = PortalApiKey::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Key',
            'key_hash' => Hash::make('test'),
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);

        $service = app(ApiKeyService::class);
        $service->revokeKey($apiKey);

        $this->assertEquals('revoked', $apiKey->fresh()->status);
    }
}
