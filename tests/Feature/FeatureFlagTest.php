<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQFeatureFlag;
use App\Models\HQFeatureFlagTarget;
use App\Domain\HQ\Services\Configuration\FeatureFlagService;

class FeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    public function test_flag_is_disabled_by_default_if_master_switch_is_off()
    {
        $flag = HQFeatureFlag::create(['name' => 'Beta Feature', 'key' => 'beta_feature', 'is_enabled' => false]);
        $service = app(FeatureFlagService::class);

        $this->assertFalse($service->isEnabled('beta_feature'));
    }

    public function test_flag_can_be_enabled_globally()
    {
        $flag = HQFeatureFlag::create(['name' => 'Beta Feature', 'key' => 'beta_feature', 'is_enabled' => true]);
        $service = app(FeatureFlagService::class);

        $this->assertTrue($service->isEnabled('beta_feature'));
    }

    public function test_flag_target_override()
    {
        $flag = HQFeatureFlag::create(['name' => 'Tenant Feature', 'key' => 'tenant_feature', 'is_enabled' => false]);
        HQFeatureFlagTarget::create([
            'feature_flag_id' => $flag->id,
            'target_type' => 'tenant',
            'target_id' => '1',
            'is_enabled' => true
        ]);

        $service = app(FeatureFlagService::class);
        $this->assertTrue($service->isEnabled('tenant_feature', ['tenant_id' => 1]));
        $this->assertFalse($service->isEnabled('tenant_feature', ['tenant_id' => 2]));
    }

    public function test_flag_recursive_json_rules()
    {
        $flag = HQFeatureFlag::create([
            'name' => 'Complex Feature',
            'key' => 'complex_feature',
            'is_enabled' => true,
            'rules' => [
                'all' => [
                    ['subscription' => 'enterprise'],
                    ['metric' => 'region', 'operator' => '==', 'value' => 'eu']
                ]
            ]
        ]);

        $service = app(FeatureFlagService::class);

        $this->assertTrue($service->isEnabled('complex_feature', [
            'subscription' => 'enterprise',
            'region' => 'eu'
        ]));

        $this->assertFalse($service->isEnabled('complex_feature', [
            'subscription' => 'pro',
            'region' => 'eu'
        ]));

        $this->assertFalse($service->isEnabled('complex_feature', [
            'subscription' => 'enterprise',
            'region' => 'us'
        ]));
    }
}
