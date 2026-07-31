<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domain\HQ\Services\Configuration\SecretVaultService;

class SecretVaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_and_retrieve_secret()
    {
        $service = app(SecretVaultService::class);
        $vault = $service->store('Stripe Key', 'stripe.api_key', 'sk_test_12345');

        // DB should not contain plain text
        $this->assertNotEquals('sk_test_12345', $vault->encrypted_value);
        $this->assertStringContainsString('ey', $vault->encrypted_value); // Usually Laravel encryption starts with base64 payload

        // Retrieve should decrypt
        $plain = $service->get('stripe.api_key');
        $this->assertEquals('sk_test_12345', $plain);
    }

    public function test_can_rotate_secret()
    {
        $service = app(SecretVaultService::class);
        $service->store('API Token', 'api.token', 'old_token');
        
        $this->assertEquals('old_token', $service->get('api.token'));

        $service->rotate('api.token', 'new_token');
        $this->assertEquals('new_token', $service->get('api.token'));
    }

    public function test_expired_secret_returns_null()
    {
        $service = app(SecretVaultService::class);
        $service->store('Temp Token', 'temp.token', 'secret', null, now()->subDay());

        $this->assertNull($service->get('temp.token'));
    }
}
