<?php

namespace App\Events\HQ\Billing;

use App\Models\HQInvoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid
{
    use Dispatchable, SerializesModels;

    public $invoice;

    public function __construct(HQInvoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
