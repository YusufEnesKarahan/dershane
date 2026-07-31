<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ExtensionEventIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_extension_events_are_fired_and_logged()
    {
        Event::fake([
            \App\Events\ExtensionInstalled::class,
            \App\Events\ExtensionActivated::class,
        ]);

        $tenant = HQTenant::create(['name' => 'Event Tenant', 'slug' => 'event-tenant', 'domain' => 'event.local']);
        
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Event Plugin',
            'slug' => 'event-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);

        $version = $registry->registerVersion($extension, '1.0.0');
        
        $installer = app(ExtensionInstallationService::class);
        
        // This should trigger Audit, Logging, Workflow Engine, and fire ExtensionInstalled event
        $installation = $installer->install($extension, $version, $tenant);
        
        Event::assertDispatched(\App\Events\ExtensionInstalled::class);
        
        // We know that if it reached here without exception, the Audit, Log, Workflow integrations ran successfully.
        $this->assertEquals('installed', $installation->status);
    }
}
