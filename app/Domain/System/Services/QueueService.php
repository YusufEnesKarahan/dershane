<?php
namespace App\Domain\System\Services;
use App\Jobs\{GenerateReportJob,ProcessDocumentJob,ProcessPaymentReminderJob,SendNotificationJob};
use Illuminate\Contracts\Queue\ShouldQueue;
class QueueService { public function dispatch(ShouldQueue $job, string $queue = 'default'): void { dispatch($job->onQueue($queue)); } public function sendNotification(int $notificationId, ?string $channel = null): void { $this->dispatch(new SendNotificationJob($notificationId, $channel), 'notifications'); } public function processDocument(int $documentId): void { $this->dispatch(new ProcessDocumentJob($documentId), 'documents'); } public function generateReport(string $title, array $payload = []): void { $this->dispatch(new GenerateReportJob($title, $payload), 'reports'); } public function processPaymentReminders(): void { $this->dispatch(new ProcessPaymentReminderJob(), 'finance'); } }
