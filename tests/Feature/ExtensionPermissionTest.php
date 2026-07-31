<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Models\HQFeatureFlag;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionRegistryService;

class ExtensionPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_install_if_feature_flag_is_disabled()
    {
        $tenant = HQTenant::create(['name' => 'Flag Tenant', 'slug' => 'flag-tenant', 'domain' => 'flag.local']);
        
        $registry = app(ExtensionRegistryService::class);
        $extension = $registry->registerExtension([
            'name' => 'Secret Plugin',
            'slug' => 'secret-plugin',
            'vendor' => 'HQ',
            'type' => 'plugin'
        ]);

        $version = $registry->registerVersion($extension, '1.0.0');

        HQFeatureFlag::create([
            'name' => 'Allow Secret Plugin',
            'key' => 'allow_extension_secret-plugin',
            'is_enabled' => false,
            'rules' => []
        ]);
        
        $installer = app(ExtensionInstallationService::class);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Extension installation is disabled by feature flag.');
        
        $installer->install($extension, $version, $tenant);
    }
}
