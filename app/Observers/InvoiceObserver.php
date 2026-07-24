<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Cache;

class InvoiceObserver
{
    public function saved(Invoice $invoice)
    {
        Cache::forget('finance.analytics.summary');
        if (strtolower($invoice->status) === 'overdue') event(new \App\Events\Notifications\PaymentOverdue($invoice));
    }
}
