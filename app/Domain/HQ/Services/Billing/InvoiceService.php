<?php

namespace App\Domain\HQ\Services\Billing;

use App\Models\HQInvoice;
use App\Models\HQSubscription;
use App\Models\HQTenant;
use Illuminate\Support\Str;

class InvoiceService
{
    public function generateForSubscription(HQSubscription $subscription): HQInvoice
    {
        $plan = $subscription->plan;

        return HQInvoice::create([
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'status' => 'pending',
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'metadata' => [
                'plan_name' => $plan->name,
                'billing_period' => $plan->billing_period,
            ],
        ]);
    }

    public function markAsPaid(HQInvoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
    }

    public function markAsFailed(HQInvoice $invoice)
    {
        $invoice->update(['status' => 'failed']);
    }
}
