<?php
namespace App\Listeners\System;
use App\Domain\System\Services\QueueService;
use App\Events\System\{PaymentOverdueEvent,ReportGeneratedEvent,StudentAbsenceDetectedEvent};
class DispatchAutomationJob { public function __construct(private readonly QueueService $queue) {} public function handle(object $event): void { match (true) { $event instanceof PaymentOverdueEvent => $this->queue->processPaymentReminders(), $event instanceof StudentAbsenceDetectedEvent => $this->queue->generateReport('Devamsızlık uyarısı',['student_id'=>$event->studentId,'date'=>$event->date]), $event instanceof ReportGeneratedEvent => null, default => null }; } }
