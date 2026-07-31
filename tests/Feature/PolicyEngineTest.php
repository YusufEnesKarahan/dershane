<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\HQPolicy;
use App\Models\HQTenant;
use App\Domain\HQ\Services\Governance\PolicyEngineService;
use Illuminate\Support\Facades\Event;

class PolicyEngineTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_evaluate_simple_policy_successfully()
    {
        $policy = HQPolicy::create([
            'name' => 'Test Backup Policy',
            'type' => 'operational',
            'logic' => [
                'metric' => 'backup.success',
                'operator' => '==',
                'value' => true
            ]
        ]);

        $service = app(PolicyEngineService::class);
        $this->assertTrue($service->evaluate($policy, ['backup.success' => true]));
        $this->assertFalse($service->evaluate($policy, ['backup.success' => false]));
    }

    public function test_can_evaluate_nested_policy_with_all_operator()
    {
        $policy = HQPolicy::create([
            'name' => 'Strict Security Policy',
            'type' => 'security',
            'logic' => [
                'all' => [
                    ['metric' => 'license.active', 'operator' => '==', 'value' => true],
                    ['metric' => 'firewall.enabled', 'operator' => '==', 'value' => true]
                ]
            ]
        ]);

        $service = app(PolicyEngineService::class);
        $this->assertTrue($service->evaluate($policy, ['license.active' => true, 'firewall.enabled' => true]));
        $this->assertFalse($service->evaluate($policy, ['license.active' => true, 'firewall.enabled' => false]));
        $this->assertFalse($service->evaluate($policy, ['license.active' => false, 'firewall.enabled' => true]));
    }

    public function test_can_evaluate_nested_policy_with_any_operator()
    {
        $policy = HQPolicy::create([
            'name' => 'Loose Access Policy',
            'type' => 'security',
            'logic' => [
                'any' => [
                    ['metric' => 'role', 'operator' => '==', 'value' => 'admin'],
                    ['metric' => 'role', 'operator' => '==', 'value' => 'manager']
                ]
            ]
        ]);

        $service = app(PolicyEngineService::class);
        $this->assertTrue($service->evaluate($policy, ['role' => 'admin']));
        $this->assertTrue($service->evaluate($policy, ['role' => 'manager']));
        $this->assertFalse($service->evaluate($policy, ['role' => 'user']));
    }

    public function test_evaluator_dispatches_events()
    {
        Event::fake([\App\Events\PolicyPassed::class, \App\Events\PolicyFailed::class]);

        $policy = HQPolicy::create([
            'name' => 'Event Policy',
            'type' => 'test',
            'logic' => ['metric' => 'foo', 'operator' => '==', 'value' => 'bar']
        ]);

        $service = app(PolicyEngineService::class);
        
        $service->evaluate($policy, ['foo' => 'bar']);
        Event::assertDispatched(\App\Events\PolicyPassed::class);

        $service->evaluate($policy, ['foo' => 'baz']);
        Event::assertDispatched(\App\Events\PolicyFailed::class);
    }

    public function test_validate_rejects_invalid_operators()
    {
        $service = app(PolicyEngineService::class);
        
        $validLogic = ['metric' => 'a', 'operator' => '==', 'value' => 'b'];
        $this->assertTrue($service->validate($validLogic));

        $invalidLogic = ['metric' => 'a', 'operator' => 'exec', 'value' => 'rm -rf /'];
        $this->assertFalse($service->validate($invalidLogic));
    }
}
