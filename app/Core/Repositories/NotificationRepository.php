<?php

namespace App\Core\Repositories;

use App\Models\Notification;
use App\Core\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Notification::with(['user'])->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }
}
