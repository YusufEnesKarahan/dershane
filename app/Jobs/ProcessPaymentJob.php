<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQInvoice;
use App\Domain\HQ\Services\Billing\PaymentProviders\PaymentProviderInterface;
use App\Events\PaymentReceived;
use App\Events\PaymentFailed;
use App\Models\HQPaymentEvent;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invoice;
    public $providerName;

    public $tries = 3;

    public function __construct(HQInvoice $invoice, string $providerName = 'mock')
    {
        $this->invoice = $invoice;
        $this->providerName = $providerName;
    }

    public function handle(PaymentProviderInterface $provider)
    {
        $response = $provider->processPayment($this->invoice);

        HQPaymentEvent::create([
            'tenant_id' => $this->invoice->tenant_id,
            'provider' => $provider->getProviderName(),
            'event_type' => $response['status'] === 'success' ? 'payment_succeeded' : 'payment_failed',
            'payload' => $response,
            'status' => 'processed',
        ]);

        if ($response['status'] === 'success') {
            app(\App\Domain\HQ\Services\Billing\InvoiceService::class)->markAsPaid($this->invoice);
            event(new PaymentReceived($this->invoice));
        } else {
            app(\App\Domain\HQ\Services\Billing\InvoiceService::class)->markAsFailed($this->invoice);
            event(new PaymentFailed($this->invoice, $response['message']));
            
            // Depending on business logic, we could downgrade or suspend here
        }
    }
}
