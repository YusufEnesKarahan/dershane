<?php
namespace App\Jobs;
use App\DTOs\System\CreateJobLogDTO;
use App\Domain\Notification\Services\NotificationService;
use App\Domain\System\Services\JobMonitoringService;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class SendNotificationJob implements ShouldQueue { use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; public int $tries=3; public function __construct(public readonly int $notificationId, public readonly ?string $channel=null) {} public function handle(NotificationService $notifications, JobMonitoringService $monitoring): void { $history=$monitoring->start(new CreateJobLogDTO(self::class,'running',['notification_id'=>$this->notificationId,'channel'=>$this->channel])); try { $notifications->send(Notification::findOrFail($this->notificationId),$this->channel); $monitoring->complete($history); } catch (\Throwable $e) { $monitoring->fail($history,$e); throw $e; } } }
