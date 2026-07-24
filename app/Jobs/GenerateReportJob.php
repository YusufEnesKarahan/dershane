<?php
namespace App\Jobs;
use App\DTOs\System\CreateJobLogDTO;
use App\Domain\Reporting\Services\ReportingService;
use App\Domain\System\Services\JobMonitoringService;
use App\Events\System\ReportGeneratedEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class GenerateReportJob implements ShouldQueue { use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; public function __construct(public readonly string $title, public readonly array $payload=[]) {} public function handle(ReportingService $reports, JobMonitoringService $monitoring): void { $history=$monitoring->start(new CreateJobLogDTO(self::class,'running',$this->payload)); try { $report=$reports->createExecutiveReport($this->title,'Sistem otomasyonu ile oluşturuldu.',$this->payload+['generated_at'=>now()->toIso8601String()]); event(new ReportGeneratedEvent($report->id)); $monitoring->complete($history); } catch (\Throwable $e) { $monitoring->fail($history,$e); throw $e; } } }
