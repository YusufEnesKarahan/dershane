<?php
namespace App\Core\Repositories\Interfaces;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;
interface NotificationLogRepositoryInterface { public function create(array $data): NotificationLog; public function recent(int $limit = 15): Collection; }
