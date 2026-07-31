<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQSubscription;
use App\Domain\HQ\Services\Billing\InvoiceService;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subscription;

    public $tries = 3;

    public function __construct(HQSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function handle(InvoiceService $invoiceService)
    {
        $invoice = $invoiceService->generateForSubscription($this->subscription);

        // Immediately dispatch payment processing
        ProcessPaymentJob::dispatch($invoice);
    }
}
