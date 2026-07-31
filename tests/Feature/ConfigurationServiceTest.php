<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQConfigurationGroup;
use App\Domain\HQ\Services\Configuration\ConfigurationService;

class ConfigurationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_set_and_get_global_configuration()
    {
        $group = HQConfigurationGroup::create(['name' => 'General', 'slug' => 'general']);
        $service = app(ConfigurationService::class);

        $config = $service->set('app.name', 'HQ Central', $group->id);
        $this->assertEquals('HQ Central', $config->value);

        // Fetch using get
        $fetched = $service->get('app.name');
        $this->assertEquals('HQ Central', $fetched);
    }

    public function test_tenant_overrides_global_configuration()
    {
        $group = HQConfigurationGroup::create(['name' => 'General', 'slug' => 'general']);
        $tenant = \App\Models\HQTenant::create(['name' => 'Tenant 1', 'slug' => 't1', 'domain' => 't1.local']);
        $service = app(ConfigurationService::class);

        // Global
        $service->set('theme', 'dark', $group->id);
        
        // Tenant 1
        $service->set('theme', 'light', $group->id, $tenant->id);

        $this->assertEquals('dark', $service->get('theme'));
        $this->assertEquals('light', $service->get('theme', $tenant->id));
        $this->assertEquals('dark', $service->get('theme', 2)); // Tenant 2 gets global
    }

    public function test_bulk_update_and_cache_invalidation()
    {
        $group = HQConfigurationGroup::create(['name' => 'Settings', 'slug' => 'settings']);
        $service = app(ConfigurationService::class);

        $service->bulkUpdate([
            'site.title' => 'My Site',
            'site.active' => true,
            'site.max_users' => 100
        ], $group->id);

        $this->assertEquals('My Site', $service->get('site.title'));
        $this->assertTrue($service->get('site.active'));
        $this->assertEquals(100, $service->get('site.max_users'));
    }

    public function test_deleting_configuration_clears_cache()
    {
        $group = HQConfigurationGroup::create(['name' => 'Temp', 'slug' => 'temp']);
        $service = app(ConfigurationService::class);
        $service->set('temp.key', 'value123', $group->id);

        $this->assertEquals('value123', $service->get('temp.key'));
        
        $service->delete('temp.key');
        
        $this->assertNull($service->get('temp.key'));
    }
}
