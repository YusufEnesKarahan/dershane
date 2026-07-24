# Queue System

The default queue connection is Laravel's database driver (`QUEUE_CONNECTION=database`), backed by the existing `jobs` and `failed_jobs` tables. Redis remains configured as an interchangeable connection.

`SendNotificationJob`, `GenerateReportJob`, `ProcessDocumentJob`, and `ProcessPaymentReminderJob` use `JobMonitoringService` to write lifecycle data to `job_histories`. Failed queue attempts remain in Laravel's `failed_jobs` table and are displayed in the System Management screen.

Run a worker with `php artisan queue:work database --queue=notifications,reports,documents,finance,default`.
