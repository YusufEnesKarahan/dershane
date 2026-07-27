<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Queue;
use Illuminate\Events\CallQueuedListener;
use Tests\TestCase;
use App\Jobs\ExportReportJob;
use App\Jobs\SendNotificationJob;
use App\Events\Notifications\StudentRegistered;
use App\Events\System\PaymentOverdueEvent;
use App\Listeners\Notifications\CreateDomainNotification;
use App\Listeners\System\DispatchAutomationJob;
use App\Models\Student;
use App\Models\ReportExport;
use App\Domain\Reporting\Services\ReportingService;
use App\DTOs\Reporting\ReportExportDTO;
use App\Domain\Reporting\Actions\ExportReport;
use Mockery;

class QueueProcessingTest extends TestCase
{
    public function test_export_report_action_dispatches_export_report_job_to_reports_queue()
    {
        Queue::fake();

        $reportingService = Mockery::mock(ReportingService::class);
        $exportMock = new ReportExport();
        $exportMock->id = 99;

        $reportingService->shouldReceive('createExport')->once()->andReturn($exportMock);

        $action = new ExportReport($reportingService);
        $dto = new ReportExportDTO('financial_summary', 'PDF', 1);

        $action->execute($dto);

        Queue::assertPushedOn('reports', ExportReportJob::class);
    }

    public function test_queue_service_dispatches_send_notification_job_to_notifications_queue()
    {
        Queue::fake();

        $queueService = app(\App\Domain\System\Services\QueueService::class);
        $queueService->sendNotification(1, 'email');

        Queue::assertPushedOn('notifications', SendNotificationJob::class);
    }

    public function test_student_registered_event_triggers_queued_notification_listener()
    {
        Queue::fake();

        $student = new Student();
        $student->id = 1;
        $student->full_name = 'Ahmet Yılmaz';

        event(new StudentRegistered($student));

        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class === CreateDomainNotification::class;
        });
    }

    public function test_payment_overdue_event_triggers_queued_automation_listener()
    {
        Queue::fake();

        event(new PaymentOverdueEvent(1));

        Queue::assertPushed(CallQueuedListener::class, function ($job) {
            return $job->class === DispatchAutomationJob::class;
        });
    }
}
