<?php

namespace App\Domain\HQ\Contracts;

use App\Models\HQInvoice;
use App\Models\HQPayment;

interface PaymentGatewayInterface
{
    /**
     * Attempt to charge a payment for an invoice.
     */
    public function createPayment(HQInvoice $invoice, array $paymentDetails = []): HQPayment;

    /**
     * Verify the status of an existing payment.
     */
    public function verifyPayment(HQPayment $payment): bool;

    /**
     * Refund a successful payment.
     */
    public function refund(HQPayment $payment): bool;
}
