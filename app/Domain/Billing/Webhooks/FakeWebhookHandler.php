<?php

namespace App\Domain\Billing\Webhooks;

use App\Domain\Billing\Services\BillingService;
use App\Models\SubscriptionPayment;

class FakeWebhookHandler implements WebhookHandlerInterface
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    public function handle(array $payload): bool
    {
        // For testing, payload might contain payment_id and status
        if (!isset($payload['payment_id']) || !isset($payload['status'])) {
            return false;
        }

        $payment = SubscriptionPayment::find($payload['payment_id']);
        if (!$payment) {
            return false;
        }

        try {
            if ($payload['status'] === 'success') {
                $this->billingService->completePayment($payment, $payload['idempotency_key'] ?? null);
            } elseif ($payload['status'] === 'failed') {
                $this->billingService->failPayment($payment, $payload['idempotency_key'] ?? null);
            }
            return true;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
