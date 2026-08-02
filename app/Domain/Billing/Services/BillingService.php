<?php

namespace App\Domain\Billing\Services;

use App\Domain\Billing\Gateways\PaymentGatewayInterface;
use App\Domain\Platform\Services\SubscriptionService;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionInvoice;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BillingService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Create a pending payment for a subscription.
     */
    public function createSubscriptionPayment(Subscription $subscription, array $paymentData): SubscriptionPayment
    {
        return DB::transaction(function () use ($subscription, $paymentData) {
            $payment = SubscriptionPayment::create([
                'branch_id' => $subscription->license->metadata['branch_id'] ?? $subscription->license->branch_id ?? session('active_branch_id'),
                'subscription_id' => $subscription->id,
                'amount' => $paymentData['amount'] ?? $subscription->price,
                'currency' => $paymentData['currency'] ?? 'TRY',
                'status' => PaymentStatus::PENDING,
                'gateway' => 'FakeGateway', // Just for the simulation
            ]);

            $gatewayResponse = $this->gateway->createPayment([
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ]);

            $payment->update([
                'transaction_id' => $gatewayResponse['transaction_id'] ?? null,
            ]);

            return $payment;
        });
    }

    /**
     * Mark payment as completed, activate subscription, and generate invoice.
     */
    public function completePayment(SubscriptionPayment $payment, ?string $idempotencyKey = null): SubscriptionPayment
    {
        return DB::transaction(function () use ($payment, $idempotencyKey) {
            if ($payment->status === PaymentStatus::PAID) {
                return $payment; // Already paid
            }

            if ($idempotencyKey) {
                $transaction = PaymentTransaction::where('idempotency_key', $idempotencyKey)->first();
                if ($transaction) {
                    return $payment; // Idempotent block: transaction already processed
                }
            }

            if ($payment->transaction_id && !$this->gateway->verifyPayment($payment->transaction_id)) {
                return $this->failPayment($payment, $idempotencyKey);
            }

            $payment->update([
                'status' => PaymentStatus::PAID,
                'paid_at' => Carbon::now(),
            ]);

            if ($idempotencyKey) {
                PaymentTransaction::create([
                    'subscription_payment_id' => $payment->id,
                    'gateway' => $payment->gateway,
                    'transaction_id' => $payment->transaction_id,
                    'idempotency_key' => $idempotencyKey,
                    'status' => 'success',
                ]);
            }

            // Activate the subscription
            if ($payment->subscription) {
                $this->subscriptionService->activateSubscription($payment->subscription->license, $payment->subscription->plan);
            }

            // Generate Invoice
            SubscriptionInvoice::create([
                'branch_id' => $payment->branch_id,
                'payment_id' => $payment->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'amount' => $payment->amount,
                'status' => 'issued',
                'issued_at' => Carbon::now(),
            ]);

            return $payment;
        });
    }

    /**
     * Mark a payment as failed.
     */
    public function failPayment(SubscriptionPayment $payment, ?string $idempotencyKey = null): SubscriptionPayment
    {
        if ($payment->status === PaymentStatus::PAID) {
            throw new \Exception("Cannot fail a payment that is already paid.");
        }

        return DB::transaction(function () use ($payment, $idempotencyKey) {
            $payment->update(['status' => PaymentStatus::FAILED]);

            if ($idempotencyKey) {
                PaymentTransaction::create([
                    'subscription_payment_id' => $payment->id,
                    'gateway' => $payment->gateway,
                    'transaction_id' => $payment->transaction_id,
                    'idempotency_key' => $idempotencyKey,
                    'status' => 'failed',
                ]);
            }

            return $payment;
        });
    }

    /**
     * Refund a payment.
     */
    public function refundPayment(SubscriptionPayment $payment): SubscriptionPayment
    {
        return DB::transaction(function () use ($payment) {
            if ($payment->status !== PaymentStatus::PAID) {
                throw new \Exception('Only paid transactions can be refunded.');
            }

            if ($this->gateway->refund($payment->transaction_id)) {
                $payment->update(['status' => PaymentStatus::REFUNDED]);
                
                // Cancel invoice if exists
                if ($payment->invoice) {
                    $payment->invoice->update(['status' => 'cancelled']);
                }
            }

            return $payment;
        });
    }
}
