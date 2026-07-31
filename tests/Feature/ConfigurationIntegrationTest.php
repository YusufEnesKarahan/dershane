<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domain\HQ\Services\HQSchedulerService;
use App\Models\HQSecretVault;
use App\Events\SecretExpired;
use Illuminate\Support\Facades\Event;

class ConfigurationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_expires_secrets()
    {
        Event::fake([SecretExpired::class]);

        HQSecretVault::create([
            'name' => 'Will Expire',
            'key' => 'expiring.secret',
            'encrypted_value' => 'enc123',
            'expires_at' => now()->subMinute(),
            'is_active' => true
        ]);

        $scheduler = app(HQSchedulerService::class);
        
        $reflection = new \ReflectionClass(HQSchedulerService::class);
        $method = $reflection->getMethod('runConfigurationChecks');
        $method->setAccessible(true);
        $method->invoke($scheduler);

        $this->assertFalse(HQSecretVault::where('key', 'expiring.secret')->first()->is_active);
        Event::assertDispatched(SecretExpired::class);
    }
}
