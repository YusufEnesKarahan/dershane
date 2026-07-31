<?php

namespace App\Domain\HQ\Services\Billing\PaymentProviders;

use App\Models\HQInvoice;
use Illuminate\Support\Str;

class MockPaymentProvider implements PaymentProviderInterface
{
    public function processPayment(HQInvoice $invoice, array $paymentMethodDetails = []): array
    {
        // Simulate external API call
        $isSuccess = !isset($paymentMethodDetails['force_fail']) || $paymentMethodDetails['force_fail'] !== true;

        if ($isSuccess) {
            return [
                'status' => 'success',
                'transaction_id' => 'mock_txn_' . Str::random(10),
                'message' => 'Payment processed successfully.',
            ];
        }

        return [
            'status' => 'failed',
            'transaction_id' => null,
            'message' => 'Payment failed due to insufficient funds or mocked failure.',
        ];
    }

    public function getProviderName(): string
    {
        return 'mock';
    }
}
