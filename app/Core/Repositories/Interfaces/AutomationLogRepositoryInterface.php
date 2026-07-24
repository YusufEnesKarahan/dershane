<?php
namespace App\Core\Repositories\Interfaces;
use App\Models\AutomationLog;
use Illuminate\Pagination\LengthAwarePaginator;
interface AutomationLogRepositoryInterface { public function create(array $data): AutomationLog; public function update(AutomationLog $log, array $data): AutomationLog; public function paginate(int $perPage = 20): LengthAwarePaginator; }
