<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function create(array $data): Notification;
    public function findForUser(int $id, int $userId): ?Notification;
    public function markRead(Notification $notification): Notification;
}
