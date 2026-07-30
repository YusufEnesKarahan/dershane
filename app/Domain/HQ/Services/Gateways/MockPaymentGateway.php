<?php

namespace App\Domain\HQ\Services\Gateways;

use App\Domain\HQ\Contracts\PaymentGatewayInterface;
use App\Models\HQInvoice;
use App\Models\HQPayment;
use App\Domain\HQ\Services\HQBillingService;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Create a mock payment for an invoice.
     */
    public function createPayment(HQInvoice $invoice, array $paymentDetails = []): HQPayment
    {
        // For mock purposes, assume it's always successful unless specified in details
        $shouldFail = $paymentDetails['should_fail'] ?? false;
        
        $status = $shouldFail ? 'failed' : 'successful';
        
        $payment = HQPayment::create([
            'invoice_id' => $invoice->id,
            'provider' => 'mock',
            'transaction_id' => 'mock_txn_' . Str::random(10),
            'amount' => $invoice->amount,
            'status' => $status,
            'paid_at' => $shouldFail ? null : now(),
            'metadata' => $paymentDetails,
        ]);

        $billingService = app(HQBillingService::class);
        if ($status === 'successful') {
            $billingService->markPaid($invoice);
        } else {
            $billingService->markFailed($invoice);
        }

        return $payment;
    }

    /**
     * Verify a payment status (mock always returns true if status is successful).
     */
    public function verifyPayment(HQPayment $payment): bool
    {
        return $payment->status === 'successful';
    }

    /**
     * Refund a mock payment.
     */
    public function refund(HQPayment $payment): bool
    {
        if ($payment->status === 'successful') {
            $payment->update([
                'status' => 'refunded',
                'metadata' => array_merge($payment->metadata ?? [], ['refunded_at' => now()->toIso8601String()]),
            ]);
            return true;
        }
        
        return false;
    }
}
