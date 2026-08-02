<?php

namespace Tests\Feature;

use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Services\BillingService;
use App\Domain\Billing\Webhooks\FakeWebhookHandler;
use App\Domain\Platform\Services\SubscriptionService;
use App\Models\Branch;
use App\Models\License;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BillingWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupSaaSTenant();
    }

    protected function createMockPayment(): SubscriptionPayment
    {
        $plan = Plan::firstOrCreate(
            ['slug' => 'pro'],
            ['name' => 'Pro', 'price' => 500, 'is_active' => true]
        );
        $license = License::create([
            'license_key' => 'TEST-LICENSE-' . uniqid(),
            'status' => 'trial',
            'plan_id' => $plan->id,
            'plan' => $plan->slug,
            'branch_id' => $this->branch->id,
            'metadata' => ['branch_id' => $this->branch->id]
        ]);

        $subscription = app(SubscriptionService::class)->startTrial($license, $plan, 14);
        
        $billingService = app(BillingService::class);
        return $billingService->createSubscriptionPayment($subscription, ['amount' => 500]);
    }

    public function test_webhook_successfully_completes_payment()
    {
        $payment = $this->createMockPayment();
        $handler = app(FakeWebhookHandler::class);

        $payload = [
            'payment_id' => $payment->id,
            'status' => 'success',
            'idempotency_key' => 'idemp-key-1'
        ];

        $result = $handler->handle($payload);

        $this->assertTrue($result);
        $this->assertEquals(PaymentStatus::PAID, $payment->refresh()->status);
        $this->assertDatabaseHas('payment_transactions', [
            'idempotency_key' => 'idemp-key-1',
            'status' => 'success',
            'subscription_payment_id' => $payment->id
        ]);
    }

    public function test_idempotency_prevents_duplicate_processing()
    {
        $payment = $this->createMockPayment();
        $handler = app(FakeWebhookHandler::class);

        $payload = [
            'payment_id' => $payment->id,
            'status' => 'success',
            'idempotency_key' => 'idemp-key-2'
        ];

        // First attempt should succeed
        $this->assertTrue($handler->handle($payload));

        // Let's create a second transaction object manually just to check if the idempotency catches it during completePayment if attempted directly
        // Or we can just call handle again, which should also be fine but since it's already PAID it will skip processing.
        // Let's simulate a pending payment but with the same idempotency key in DB.
        
        $payment2 = $this->createMockPayment();
        // Insert fake transaction manually
        \App\Models\PaymentTransaction::create([
            'subscription_payment_id' => $payment2->id,
            'gateway' => 'FakeGateway',
            'transaction_id' => 'tx-dummy',
            'idempotency_key' => 'idemp-key-3',
            'status' => 'success'
        ]);

        // Trying to process with idemp-key-3 should be skipped due to idempotency block
        $payload2 = [
            'payment_id' => $payment2->id,
            'status' => 'success',
            'idempotency_key' => 'idemp-key-3'
        ];
        
        $handler->handle($payload2);

        // It should still be pending because the service saw the idempotency key and returned early
        $this->assertEquals(PaymentStatus::PENDING, $payment2->refresh()->status);
    }

    public function test_failed_payment_can_be_retried()
    {
        $payment = $this->createMockPayment();
        $handler = app(FakeWebhookHandler::class);

        $failPayload = [
            'payment_id' => $payment->id,
            'status' => 'failed',
            'idempotency_key' => 'fail-key-1'
        ];

        $this->assertTrue($handler->handle($failPayload));
        $this->assertEquals(PaymentStatus::FAILED, $payment->refresh()->status);

        // Retry with a different idempotency key
        $successPayload = [
            'payment_id' => $payment->id,
            'status' => 'success',
            'idempotency_key' => 'success-key-1'
        ];

        $this->assertTrue($handler->handle($successPayload));
        $this->assertEquals(PaymentStatus::PAID, $payment->refresh()->status);
    }

    public function test_expired_subscription_command()
    {
        $payment = $this->createMockPayment();
        $subscription = $payment->subscription;
        
        // Force it to be active but expired yesterday
        $subscription->update([
            'status' => 'active',
            'trial_ends_at' => null,
            'ends_at' => now()->subDay()
        ]);
        $subscription->license->update([
            'status' => 'active',
            'expires_at' => now()->subDay()
        ]);

        $this->artisan('billing:check-expired')
            ->expectsOutputToContain('Completed!')
            ->assertSuccessful();

        $this->assertEquals('expired', $subscription->refresh()->status);
        $this->assertEquals('expired', $subscription->license->refresh()->status);
    }

    public function test_tenant_isolation_on_transactions()
    {
        $payment1 = $this->createMockPayment();

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
        
        $billingService = app(BillingService::class);
        $payment2 = $billingService->createSubscriptionPayment($subscription2, ['amount' => 500]);

        $handler = app(FakeWebhookHandler::class);
        $handler->handle(['payment_id' => $payment2->id, 'status' => 'success', 'idempotency_key' => 'idk-tenant2']);

        // Since we are in Branch 2, we shouldn't see Branch 1's payment/transactions using Eloquent all()
        $visiblePayments = SubscriptionPayment::all();
        $this->assertCount(1, $visiblePayments);
        $this->assertEquals($payment2->id, $visiblePayments->first()->id);
        
        // Transactions belongsTo Payment. So accessing transactions via payment works, but if we query directly?
        // PaymentTransaction itself doesn't have HasBranch/TenantScoped in the implementation plan, but it relies on subscription_payment_id.
        // Let's ensure tenant B can't find tenant A's payment directly
        $this->assertNull(SubscriptionPayment::find($payment1->id));
    }
}
