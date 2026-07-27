<?php
namespace App\Jobs;
use App\DTOs\Notification\CreateNotificationDTO;
use App\DTOs\System\CreateJobLogDTO;
use App\Domain\Notification\Services\NotificationService;
use App\Domain\System\Services\JobMonitoringService;
use App\Models\{Invoice,ParentStudent};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class ProcessPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 10;

    public function __construct()
    {
        $this->onQueue('finance');
    }

    public function handle(NotificationService $notifications, JobMonitoringService $monitoring): void
    {
        $history = $monitoring->start(new CreateJobLogDTO(self::class, 'running'));
        try {
            Invoice::query()
                ->whereDate('due_date', '<', today())
                ->whereColumn('paid_amount', '<', 'total_amount')
                ->with('student')
                ->each(function (Invoice $invoice) use ($notifications): void {
                    ParentStudent::query()
                        ->where('student_id', $invoice->student_id)
                        ->pluck('parent_id')
                        ->each(fn (int $userId) => $notifications->create(new CreateNotificationDTO($userId, 'Ödeme hatırlatması', $invoice->invoice_number . ' numaralı faturanın vadesi geçmiştir.', 'finance', 'panel', 'high', ['invoice_id' => $invoice->id])));
                });
            $monitoring->complete($history);
        } catch (\Throwable $e) {
            $monitoring->fail($history, $e);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('ProcessPaymentReminderJob failed: ' . $exception->getMessage());
    }
}
