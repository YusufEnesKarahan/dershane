# Sprint 4.8 Walkthrough

1. Open **Admin / Bildirim Merkezi** for totals and channel distribution.
2. Send a panel, email, or SMS-ready notification from the list.
3. Create templates with `{{name}}`, then review analytics and delivery logs.
4. New students, overdue invoices, exam results, assignments, and CRM follow-ups produce event-driven notifications.

## Sprint 4.9 Queue & Automation

Open **System / Queue Dashboard** to inspect pending, failed, and completed jobs. Automation Logs records daily payment, exam, attendance, and CRM scheduling runs. Start workers with `php artisan queue:work database` and the scheduler with `php artisan schedule:work`.
