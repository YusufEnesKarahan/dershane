<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;
use App\Domain\HQ\Services\Configuration\ConfigurationService;

class ExtensionTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_b_cannot_access_tenant_a_extension_data()
    {
        $tenantA = HQTenant::create(['name' => 'Tenant A', 'slug' => 'tenant-a', 'domain' => 'a.local']);
        $tenantB = HQTenant::create(['name' => 'Tenant B', 'slug' => 'tenant-b', 'domain' => 'b.local']);
        
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Isolated Plugin',
            'slug' => 'isolated-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin',
            'metadata' => [
                'default_config' => ['api_key' => 'secret_for_a']
            ]
        ]);

        \App\Models\HQConfigurationGroup::create(['id' => 1, 'name' => 'General', 'slug' => 'general']);

        $version = $registry->registerVersion($extension, '1.0.0');
        
        $installer = app(ExtensionInstallationService::class);
        
        // Tenant A installs
        $installationA = $installer->install($extension, $version, $tenantA);
        $installer->enable($installationA);

        // Assert Tenant A has config
        $configService = app(ConfigurationService::class);
        $configA = $configService->get('ext_isolated-plugin_api_key', $tenantA->id);
        $this->assertEquals('secret_for_a', $configA);

        // Assert Tenant B DOES NOT have config
        $configB = $configService->get('ext_isolated-plugin_api_key', $tenantB->id);
        $this->assertNull($configB);

        // Assert Tenant B DOES NOT have installation
        $installationB = \App\Models\HQExtensionInstallation::where('tenant_id', $tenantB->id)
            ->where('extension_id', $extension->id)
            ->first();
        
        $this->assertNull($installationB);

        // Tenant A has it
        $checkA = \App\Models\HQExtensionInstallation::where('tenant_id', $tenantA->id)
            ->where('extension_id', $extension->id)
            ->first();
        
        $this->assertNotNull($checkA);
    }
}
