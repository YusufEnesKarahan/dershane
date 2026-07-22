# Payment & Collection System

Handles payment transactions, installment plans, and refunds:

- **Payments (`payments`)**: Unique `payment_number`, invoice reference, student reference, payment method (Cash, Credit Card, Bank Transfer), amount, and timestamp.
- **Invoice & Debt Auto-Sync**: Recording a payment updates invoice `paid_amount` and transitions invoice and debt status (`Pending` -> `Partial` -> `Paid`).
- **Payment Plans (`payment_plans`)**: Manages installment schedules for long-term tuition billing.
- **Refunds (`refunds`)**: Tracks refund records associated with processed payments.
