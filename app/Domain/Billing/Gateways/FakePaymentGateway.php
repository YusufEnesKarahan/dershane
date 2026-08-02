<?php

namespace App\Domain\Billing\Gateways;

use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(array $data): array
    {
        // Simulate an external gateway call and return a fake transaction ID
        return [
            'status' => 'success',
            'transaction_id' => 'FAKE-TXN-' . strtoupper(Str::random(12)),
            'payment_url' => 'https://fake-gateway.example.com/pay',
        ];
    }

    public function verifyPayment(string $transactionId): bool
    {
        // Fake gateway always says yes for successful tests
        return true;
    }

    public function refund(string $transactionId): bool
    {
        // Fake gateway always succeeds refunds
        return true;
    }
}
