<?php
namespace App\Events\Notifications;
use App\Models\Invoice;
class PaymentOverdue { public function __construct(public readonly Invoice $invoice) {} }
