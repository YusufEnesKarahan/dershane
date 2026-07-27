<?php
namespace App\Listeners\System;

use App\Domain\System\Services\QueueService;
use App\Events\System\{PaymentOverdueEvent,ReportGeneratedEvent,StudentAbsenceDetectedEvent};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class DispatchAutomationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    public function __construct(private readonly QueueService $queueService) {}

    public function handle(object $event): void
    {
        match (true) {
            $event instanceof PaymentOverdueEvent => $this->queueService->processPaymentReminders(),
            $event instanceof StudentAbsenceDetectedEvent => $this->queueService->generateReport('Devamsızlık uyarısı', ['student_id' => $event->studentId, 'date' => $event->date]),
            $event instanceof ReportGeneratedEvent => null,
            default => null
        };
    }
}
