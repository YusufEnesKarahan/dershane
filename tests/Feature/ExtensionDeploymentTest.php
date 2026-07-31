<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;

class ExtensionDeploymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_extension_version()
    {
        $tenant = HQTenant::create(['name' => 'Deploy Tenant', 'slug' => 'deploy-tenant', 'domain' => 'deploy.local']);
        
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Deploy Plugin',
            'slug' => 'deploy-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);

        $v1 = $registry->registerVersion($extension, '1.0.0');
        $v2 = $registry->registerVersion($extension, '2.0.0');
        
        $installer = app(ExtensionInstallationService::class);
        
        $installation = $installer->install($extension, $v1, $tenant);
        $installer->enable($installation);
        
        $this->assertEquals($v1->id, $installation->version_id);
        
        $installer->update($installation, $v2);
        
        $this->assertEquals($v2->id, $installation->version_id);
        $this->assertEquals('activated', $installation->status);
    }
}
