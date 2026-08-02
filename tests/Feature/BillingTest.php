<?php

namespace Tests\Feature;

use App\Domain\Billing\Gateways\PaymentGatewayInterface;
use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Services\BillingService;
use App\Domain\Platform\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupSaaSTenant();
    }

    protected function createMockSubscription()
    {
        $plan = Plan::create(['name' => 'Pro', 'slug' => 'pro', 'price' => 500, 'is_active' => true]);
        $license = License::create([
            'license_key' => 'TEST-LICENSE',
            'status' => 'trial',
            'plan_id' => $plan->id,
            'plan' => $plan->slug,
            'branch_id' => $this->branch->id,
            'metadata' => ['branch_id' => $this->branch->id]
        ]);

        return app(SubscriptionService::class)->startTrial($license, $plan, 14);
    }

    public function test_payment_creation()
    {
        $subscription = $this->createMockSubscription();
        $billingService = app(BillingService::class);

        $payment = $billingService->createSubscriptionPayment($subscription, [
            'amount' => 500,
            'currency' => 'TRY'
        ]);

        $this->assertDatabaseHas('subscription_payments', [
            'id' => $payment->id,
            'amount' => 500,
            'status' => 'pending'
        ]);

        $this->assertNotNull($payment->transaction_id);
    }

    public function test_successful_payment_activates_subscription()
    {
        $subscription = $this->createMockSubscription();
        $billingService = app(BillingService::class);

        $payment = $billingService->createSubscriptionPayment($subscription, ['amount' => 500]);
        
        $billingService->completePayment($payment);

        $payment->refresh();
        $subscription->refresh();
        $subscription->license->refresh();

        $this->assertEquals(PaymentStatus::PAID, $payment->status);
        $this->assertEquals('active', $subscription->status);
        $this->assertEquals('active', $subscription->license->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertDatabaseHas('subscription_invoices', [
            'payment_id' => $payment->id,
            'status' => 'issued'
        ]);
    }

    public function test_failed_payment()
    {
        $subscription = $this->createMockSubscription();
        $billingService = app(BillingService::class);

        // Mock gateway to return false for verifyPayment
        $mockGateway = \Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('createPayment')->andReturn(['transaction_id' => 'TXN-FAIL']);
        $mockGateway->shouldReceive('verifyPayment')->andReturn(false);
        $this->app->instance(PaymentGatewayInterface::class, $mockGateway);

        $billingServiceWithMock = app(BillingService::class);

        $payment = $billingServiceWithMock->createSubscriptionPayment($subscription, ['amount' => 500]);
        $billingServiceWithMock->completePayment($payment);

        $payment->refresh();

        $this->assertEquals(PaymentStatus::FAILED, $payment->status);
    }

    public function test_refund_payment()
    {
        $subscription = $this->createMockSubscription();
        $billingService = app(BillingService::class);

        $payment = $billingService->createSubscriptionPayment($subscription, ['amount' => 500]);
        $billingService->completePayment($payment);

        $this->assertEquals(PaymentStatus::PAID, $payment->refresh()->status);

        $billingService->refundPayment($payment);

        $this->assertEquals(PaymentStatus::REFUNDED, $payment->refresh()->status);
        
        if ($payment->invoice) {
            $this->assertEquals('cancelled', $payment->invoice->refresh()->status);
        }
    }

    public function test_tenant_isolation_prevents_viewing_other_payments()
    {
        $subscription1 = $this->createMockSubscription();
        $billingService = app(BillingService::class);
        $payment1 = $billingService->createSubscriptionPayment($subscription1, ['amount' => 500]);

        // Create another tenant
        $branch2 = Branch::create(['name' => 'Branch 2', 'slug' => 'branch-2']);
        \App\Core\Context\TenantContext::setActiveBranchId($branch2->id);
        
        $plan = Plan::first();
        $license2 = License::create([
            'license_key' => 'TEST-LICENSE-2',
            'status' => 'trial',
            'plan_id' => $plan->id,
            'plan' => $plan->slug,
            'branch_id' => $branch2->id,
            'metadata' => ['branch_id' => $branch2->id]
        ]);
        $subscription2 = app(SubscriptionService::class)->startTrial($license2, $plan, 14);
        
        $payment2 = $billingService->createSubscriptionPayment($subscription2, ['amount' => 500]);

        // Verify Branch 2 can't see Branch 1's payments using the model
        $visiblePayments = SubscriptionPayment::all();
        $this->assertCount(1, $visiblePayments);
        $this->assertEquals($payment2->id, $visiblePayments->first()->id);
    }
}
