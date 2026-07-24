<?php
namespace App\Domain\System\Services;
use App\Core\Repositories\Interfaces\AutomationLogRepositoryInterface;
use App\DTOs\System\AutomationLogDTO;
use App\Models\{Exam,LeadFollowup};
class AutomationService {
    public function __construct(private readonly QueueService $queue, private readonly AutomationLogRepositoryInterface $logs) {}
    public function paymentReminders(): void { $this->run('payment-reminders', fn () => $this->queue->processPaymentReminders()); }
    public function upcomingExams(): void { $this->run('upcoming-exams', function (): void { $count = Exam::query()->whereDate('exam_date', '>=', today())->whereDate('exam_date', '<=', today()->addDays(3))->count(); $this->queue->generateReport('Yaklaşan sınav bildirimi', ['exam_count' => $count]); }); }
    public function attendanceWarnings(): void { $this->run('attendance-warnings', fn () => $this->queue->generateReport('Devamsızlık uyarıları')); }
    public function pendingFollowups(): void { $this->run('pending-followups', function (): void { $count = LeadFollowup::query()->where('status', 'pending')->where('followup_date', '<=', now())->count(); $this->queue->generateReport('Bekleyen CRM takipleri', ['followup_count' => $count]); }); }
    public function weeklySystemReport(): void { $this->run('weekly-system-report', fn () => $this->queue->generateReport('Haftalık sistem raporu')); }
    public function weeklyCleanup(): void { $this->run('weekly-cleanup', fn () => \App\Models\JobHistory::where('created_at', '<', now()->subDays(90))->delete()); }
    private function run(string $name, \Closure $work): void { $log=$this->logs->create(['job_name'=>$name,'status'=>'running','payload'=>[],'started_at'=>now()]); try { $work(); $this->logs->update($log,['status'=>'completed','completed_at'=>now()]); } catch (\Throwable $e) { $this->logs->update($log,['status'=>'failed','completed_at'=>now(),'error_message'=>$e->getMessage()]); report($e); } }
}
