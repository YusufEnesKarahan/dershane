<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domain\HQ\Services\Configuration\EnvironmentProfileService;

class EnvironmentProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_setup_and_retrieve_profile_overrides()
    {
        $service = app(EnvironmentProfileService::class);
        
        $service->setupProfile('Production', 'production', [
            'debug' => false,
            'log_level' => 'error'
        ]);

        $this->assertEquals(false, $service->getOverride('production', 'debug'));
        $this->assertEquals('error', $service->getOverride('production', 'log_level'));
        $this->assertNull($service->getOverride('production', 'non_existent_key'));
    }
}
