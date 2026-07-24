<?php
namespace App\Domain\System\Services;
use App\Core\Repositories\Interfaces\JobHistoryRepositoryInterface;
use App\DTOs\System\CreateJobLogDTO;
use App\Models\JobHistory;
class JobMonitoringService { public function __construct(private readonly JobHistoryRepositoryInterface $histories) {} public function start(CreateJobLogDTO $dto): JobHistory { return $this->histories->create(['job_name'=>$dto->jobName,'status'=>'running','payload'=>$dto->payload,'started_at'=>$dto->startedAt ?? now()]); } public function complete(JobHistory $history): void { $this->histories->update($history,['status'=>'completed','completed_at'=>now()]); } public function fail(JobHistory $history, \Throwable $exception): void { $this->histories->update($history,['status'=>'failed','completed_at'=>now(),'error_message'=>$exception->getMessage()]); } }
