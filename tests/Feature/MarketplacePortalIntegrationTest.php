<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQExtension;
use App\Models\HQExtensionVersion;
use App\Models\HQExtensionInstallation;

class MarketplacePortalIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_view_installed_extensions()
    {
        $tenant = HQTenant::create(['name' => 'Test', 'slug' => 'test-marketplace', 'uuid' => \Illuminate\Support\Str::uuid()]);
        
        $extension = HQExtension::create(['name' => 'Slack Auth', 'slug' => 'slack-auth', 'vendor' => 'test', 'type' => 'plugin']);
        $version = HQExtensionVersion::create(['extension_id' => $extension->id, 'version' => '1.0.0']);
        
        HQExtensionInstallation::create([
            'tenant_id' => $tenant->id,
            'extension_id' => $extension->id,
            'version_id' => $version->id,
            'status' => 'installed'
        ]);
        
        $installations = HQExtensionInstallation::where('tenant_id', $tenant->id)
            ->with('extension', 'version')
            ->get();
            
        $this->assertCount(1, $installations);
        $this->assertEquals('Slack Auth', $installations->first()->extension->name);
    }
}
