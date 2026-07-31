<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;
use Illuminate\Support\Facades\Event;

class ExtensionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_install_and_activate_extension()
    {
        Event::fake([
            \App\Events\ExtensionInstalled::class,
            \App\Events\ExtensionActivated::class,
        ]);

        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant', 'domain' => 'test.local']);
        
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Lifecycle Plugin',
            'slug' => 'lifecycle-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);

        $version = $registry->registerVersion($extension, '1.0.0');
        
        $installer = app(ExtensionInstallationService::class);
        
        $installation = $installer->install($extension, $version, $tenant);
        $this->assertEquals('installed', $installation->status);
        
        $installer->enable($installation);
        $this->assertEquals('activated', $installation->status);

        Event::assertDispatched(\App\Events\ExtensionInstalled::class);
        Event::assertDispatched(\App\Events\ExtensionActivated::class);
    }
}
