<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Models\HQFeatureFlag;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;
use App\Domain\HQ\Services\Configuration\FeatureFlagService;

class ExtensionFeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    public function test_enterprise_tenant_can_install_while_free_cannot()
    {
        $enterpriseTenant = HQTenant::create(['name' => 'Enterprise Tenant', 'slug' => 'enterprise-tenant', 'domain' => 'ent.local']);
        $freeTenant = HQTenant::create(['name' => 'Free Tenant', 'slug' => 'free-tenant', 'domain' => 'free.local']);
        
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Enterprise Plugin',
            'slug' => 'enterprise-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);

        $version = $registry->registerVersion($extension, '1.0.0');

        // Create feature flag that is disabled globally
        $flag = HQFeatureFlag::create([
            'name' => 'Allow Enterprise Plugin',
            'key' => 'allow_extension_enterprise-plugin',
            'is_enabled' => false,
            'rules' => []
        ]);
        
        // Override for Enterprise Tenant to be true
        $flag->targets()->create([
            'target_type' => 'tenant',
            'target_id' => $enterpriseTenant->id,
            'is_enabled' => true
        ]);
        
        $installer = app(ExtensionInstallationService::class);
        
        // Enterprise can install
        $installation = $installer->install($extension, $version, $enterpriseTenant);
        $this->assertEquals('installed', $installation->status);

        // Free cannot install
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Extension installation is disabled by feature flag.');
        
        $installer->install($extension, $version, $freeTenant);
    }
}
