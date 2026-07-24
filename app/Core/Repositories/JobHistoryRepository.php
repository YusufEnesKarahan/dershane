<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\JobHistoryRepositoryInterface;
use App\Models\JobHistory;
use Illuminate\Pagination\LengthAwarePaginator;
class JobHistoryRepository implements JobHistoryRepositoryInterface { public function create(array $data): JobHistory { return JobHistory::create($data); } public function update(JobHistory $history, array $data): JobHistory { $history->update($data); return $history->refresh(); } public function paginate(int $perPage = 20): LengthAwarePaginator { return JobHistory::query()->latest('started_at')->paginate($perPage); } }
