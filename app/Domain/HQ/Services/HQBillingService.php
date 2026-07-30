<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSubscription;
use App\Models\HQInvoice;
use App\Events\HQ\Billing\InvoicePaid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HQBillingService
{
    /**
     * Create an invoice for a subscription.
     */
    public function createInvoice(HQSubscription $subscription, float $amount = null): HQInvoice
    {
        $plan = $subscription->plan;
        
        $invoiceAmount = $amount ?? $plan->price;

        return HQInvoice::create([
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $invoiceAmount,
            'currency' => $plan->currency,
            'status' => 'pending',
            'issued_at' => now(),
        ]);
    }

    /**
     * Mark an invoice as paid.
     */
    public function markPaid(HQInvoice $invoice): HQInvoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            if ($invoice->subscription) {
                // Determine if we need to renew or if this is the first payment
                if ($invoice->subscription->status === 'past_due' || $invoice->subscription->ends_at <= now()) {
                    app(HQSubscriptionService::class)->renewSubscription($invoice->subscription);
                }
            }

            event(new InvoicePaid($invoice));

            return $invoice;
        });
    }

    /**
     * Mark an invoice as failed.
     */
    public function markFailed(HQInvoice $invoice): HQInvoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->update([
                'status' => 'failed',
            ]);

            if ($invoice->subscription) {
                $invoice->subscription->update([
                    'status' => 'past_due',
                ]);
            }

            return $invoice;
        });
    }

    /**
     * Generate a unique invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
