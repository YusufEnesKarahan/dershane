<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\JobHistoryRepositoryInterface;
use App\Models\JobHistory;
use Illuminate\Contracts\Pagination\Paginator;
class JobHistoryRepository implements JobHistoryRepositoryInterface { public function create(array $data): JobHistory { return JobHistory::create($data); } public function update(JobHistory $history, array $data): JobHistory { $history->update($data); return $history->refresh(); } public function paginate(int $perPage = 20): Paginator { return JobHistory::query()->latest('started_at')->simplePaginate($perPage); } }
