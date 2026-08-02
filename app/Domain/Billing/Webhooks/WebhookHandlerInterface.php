<?php

namespace App\Domain\Billing\Webhooks;

interface WebhookHandlerInterface
{
    /**
     * Handle the incoming webhook payload.
     * 
     * @param array $payload The JSON payload from the gateway.
     * @return bool True if successfully processed, False otherwise.
     */
    public function handle(array $payload): bool;
}
