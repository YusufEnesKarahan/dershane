<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQExtension;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Extension\MarketplaceService;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_available_extensions()
    {
        HQExtension::create([
            'name' => 'Demo Plugin',
            'slug' => 'demo-plugin',
            'vendor' => 'HQ',
            'status' => 'active',
            'type' => 'plugin'
        ]);

        $service = app(MarketplaceService::class);
        $extensions = $service->getAvailableExtensions();

        $this->assertCount(1, $extensions);
        $this->assertEquals('demo-plugin', $extensions->first()->slug);
    }
}
