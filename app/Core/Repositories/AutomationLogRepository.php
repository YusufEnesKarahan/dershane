<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\AutomationLogRepositoryInterface;
use App\Models\AutomationLog;
use Illuminate\Contracts\Pagination\Paginator;
class AutomationLogRepository implements AutomationLogRepositoryInterface { public function create(array $data): AutomationLog { return AutomationLog::create($data); } public function update(AutomationLog $log, array $data): AutomationLog { $log->update($data); return $log->refresh(); } public function paginate(int $perPage = 20): Paginator { return AutomationLog::query()->latest('started_at')->simplePaginate($perPage); } }
