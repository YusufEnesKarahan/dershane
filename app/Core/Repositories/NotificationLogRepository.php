<?php
namespace App\Core\Repositories;
use App\Core\Repositories\Interfaces\NotificationLogRepositoryInterface;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;
class NotificationLogRepository implements NotificationLogRepositoryInterface { public function create(array $data): NotificationLog { return NotificationLog::create($data); } public function recent(int $limit = 15): Collection { return NotificationLog::query()->with('notification.user')->latest()->limit($limit)->get(); } }
