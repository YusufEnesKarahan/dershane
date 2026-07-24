<?php
namespace App\Events\System;
class PaymentOverdueEvent { public function __construct(public readonly int $invoiceId) {} }
