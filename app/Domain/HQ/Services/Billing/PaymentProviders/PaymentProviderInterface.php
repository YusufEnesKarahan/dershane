<?php

namespace App\Domain\HQ\Services\Billing\PaymentProviders;

use App\Models\HQInvoice;
use App\Models\HQTenant;

interface PaymentProviderInterface
{
    /**
     * Process a payment for a given invoice.
     *
     * @param HQInvoice $invoice
     * @param array $paymentMethodDetails
     * @return array
     */
    public function processPayment(HQInvoice $invoice, array $paymentMethodDetails = []): array;

    /**
     * Retrieve the provider name.
     *
     * @return string
     */
    public function getProviderName(): string;
}
