# Automation System

The scheduler runs daily payment, exam, attendance, and CRM follow-up automations, plus weekly reporting and cleanup. Each scheduler run records a lifecycle entry in `automation_logs`; expensive work is dispatched to queues.

Use `php artisan schedule:work` locally or a server cron calling `php artisan schedule:run` every minute.
