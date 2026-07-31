<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQConfigurationGroup;
use App\Domain\HQ\Services\Configuration\ConfigurationService;
use App\Domain\HQ\Services\Configuration\ConfigurationVersionService;
use Illuminate\Support\Facades\Event;
use App\Events\ConfigurationRollbackCompleted;

class ConfigurationRollbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_version_and_rollback()
    {
        Event::fake([ConfigurationRollbackCompleted::class]);

        $group = HQConfigurationGroup::create(['name' => 'Main', 'slug' => 'main']);
        $configService = app(ConfigurationService::class);
        $versionService = app(ConfigurationVersionService::class);

        // Set initial config
        $config = $configService->set('app.maintenance', false, $group->id);
        
        // Save version v1
        $version1 = $versionService->createVersion($config, 'v1.0');

        // Change config
        $configService->set('app.maintenance', true, $group->id);
        $this->assertTrue($configService->get('app.maintenance'));

        // Rollback using refreshed config
        $versionService->rollback($config->refresh(), $version1);

        // Fetch to ensure rollback applied
        $this->assertFalse($configService->get('app.maintenance'));
        Event::assertDispatched(ConfigurationRollbackCompleted::class);
    }
}
