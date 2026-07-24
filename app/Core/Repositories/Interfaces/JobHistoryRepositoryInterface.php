<?php
namespace App\Core\Repositories\Interfaces;
use App\Models\JobHistory;
use Illuminate\Pagination\LengthAwarePaginator;
interface JobHistoryRepositoryInterface { public function create(array $data): JobHistory; public function update(JobHistory $history, array $data): JobHistory; public function paginate(int $perPage = 20): LengthAwarePaginator; }
