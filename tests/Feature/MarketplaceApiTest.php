<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\HQTenant;
use App\Models\HQExtension;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;

class MarketplaceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_request_blocked()
    {
        // No signature
        $response = $this->getJson('/api/hq/marketplace/extensions');
        $response->assertStatus(401);
    }

    public function test_list_extensions()
    {
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Api Plugin',
            'slug' => 'api-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);

        config(['hq.api.token' => 'test-token', 'hq.api.secret' => 'test-secret']);
        $timestamp = (string) time();
        $payload = '[]';
        $signature = hash_hmac('sha256', $payload . $timestamp, 'test-secret');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => $timestamp
        ])->getJson('/api/hq/marketplace/extensions');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.slug', 'api-plugin');
    }

    public function test_install_extension_dispatch_job()
    {
        \Illuminate\Support\Facades\Queue::fake();

        $tenant = HQTenant::create(['name' => 'Api Tenant', 'slug' => 'api-tenant', 'domain' => 'api.local']);

        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Api Plugin',
            'slug' => 'api-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);
        $registry->registerVersion($extension, '1.0.0');

        $payload = [
            'extension_slug' => 'api-plugin',
            'tenant_id' => $tenant->id
        ];

        config(['hq.api.token' => 'test-token', 'hq.api.secret' => 'test-secret']);
        $timestamp = (string) time();
        $payloadStr = json_encode($payload);
        $signature = hash_hmac('sha256', $payloadStr . $timestamp, 'test-secret');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
            'X-HQ-Signature' => $signature,
            'X-HQ-Timestamp' => $timestamp
        ])->postJson('/api/hq/marketplace/install', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\InstallExtensionJob::class);
    }
}
