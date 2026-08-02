<?php

namespace App\Domain\Billing\Gateways;

interface PaymentGatewayInterface
{
    /**
     * Create a payment request with the gateway.
     * 
     * @param array $data Contains amount, currency, description, etc.
     * @return array Returns gateway specific transaction details (e.g. url, transaction_id).
     */
    public function createPayment(array $data): array;

    /**
     * Verify the status of an existing transaction.
     * 
     * @param string $transactionId The gateway's transaction ID.
     * @return bool True if payment is successful, false otherwise.
     */
    public function verifyPayment(string $transactionId): bool;

    /**
     * Refund a successful transaction.
     * 
     * @param string $transactionId The gateway's transaction ID.
     * @return bool True if refund is successful, false otherwise.
     */
    public function refund(string $transactionId): bool;
}
