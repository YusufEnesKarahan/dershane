<?php
namespace App\Core\Repositories\Interfaces;
use App\Models\AutomationLog;
use Illuminate\Contracts\Pagination\Paginator;
interface AutomationLogRepositoryInterface { public function create(array $data): AutomationLog; public function update(AutomationLog $log, array $data): AutomationLog; public function paginate(int $perPage = 20): Paginator; }
