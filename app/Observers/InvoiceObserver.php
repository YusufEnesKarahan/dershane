<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Cache;

class InvoiceObserver
{
    public function saved(Invoice $invoice)
    {
        Cache::forget('finance.analytics.summary');
    }
}
