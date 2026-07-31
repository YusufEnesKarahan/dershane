<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HQTenant;
use App\Models\HQPlan;
use App\Models\HQExtension;
use App\Models\HQExtensionVersion;
use App\Models\HQFeatureFlag;
use App\Domain\HQ\Services\Billing\SubscriptionService;
use App\Domain\HQ\Services\Billing\EntitlementService;
use App\Domain\HQ\Services\HQAuditService;
use App\Domain\HQ\Services\Extension\ExtensionInstallationService;
use App\Domain\HQ\Services\Extension\ExtensionDependencyService;
use App\Domain\HQ\Services\Extension\ExtensionLifecycleService;
use App\Domain\HQ\Services\Extension\ExtensionPermissionService;
use App\Domain\HQ\Services\Configuration\FeatureFlagService;
use Exception;

class MarketplaceBillingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_install_extension_if_entitlement_missing()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create([
            'name' => 'Free',
            'slug' => 'free',
            'features' => [], // No features
        ]);

        $subscriptionService = new SubscriptionService(new EntitlementService(), new HQAuditService());
        $subscriptionService->subscribe($tenant, $plan);

        $extension = HQExtension::create(['name' => 'Analytics', 'slug' => 'analytics', 'vendor' => 'acme', 'type' => 'plugin']);
        $version = HQExtensionVersion::create(['extension_id' => $extension->id, 'version' => '1.0.0']);

        HQFeatureFlag::create(['key' => 'billing_new_engine', 'name' => 'Billing New Engine', 'is_enabled' => true]);

        $featureFlagService = new FeatureFlagService();
        $service = new ExtensionInstallationService(
            app(ExtensionDependencyService::class),
            app(ExtensionLifecycleService::class),
            app(ExtensionPermissionService::class),
            $featureFlagService
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Extension installation is disabled by billing entitlement.");

        $service->install($extension, $version, $tenant);
    }

    public function test_can_install_extension_if_entitlement_exists()
    {
        $tenant = HQTenant::create(['name' => 'Test Tenant', 'slug' => 'test-tenant']);
        $plan = HQPlan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'features' => ['allow_extension_analytics'],
        ]);

        $subscriptionService = new SubscriptionService(new EntitlementService(), new HQAuditService());
        $subscriptionService->subscribe($tenant, $plan);

        $extension = HQExtension::create(['name' => 'Analytics', 'slug' => 'analytics', 'vendor' => 'acme', 'type' => 'plugin']);
        $version = HQExtensionVersion::create(['extension_id' => $extension->id, 'version' => '1.0.0']);

        HQFeatureFlag::create(['key' => 'billing_new_engine', 'name' => 'Billing New Engine', 'is_enabled' => true]);

        $featureFlagService = new FeatureFlagService();
        
        // Mock DependencyService to bypass checkCompatibility
        $dependencyMock = $this->createMock(ExtensionDependencyService::class);
        $dependencyMock->method('checkCompatibility')->willReturn(['is_compatible' => true]);

        $service = new ExtensionInstallationService(
            $dependencyMock,
            app(ExtensionLifecycleService::class),
            app(ExtensionPermissionService::class),
            $featureFlagService
        );

        $installation = $service->install($extension, $version, $tenant);
        
        $this->assertNotNull($installation);
        $this->assertEquals('installed', $installation->status);
    }
}
