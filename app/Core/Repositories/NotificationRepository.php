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
            $query->where(fn ($builder) => $builder->where('title', 'like', "%{$filters['search']}%")->orWhere('message', 'like', "%{$filters['search']}%"));
        }

        foreach (['channel', 'type', 'priority'] as $field) {
            if (!empty($filters[$field])) $query->where($field, $filters[$field]);
        }

        if (($filters['read'] ?? null) === 'read') $query->whereNotNull('read_at');
        if (($filters['read'] ?? null) === 'unread') $query->whereNull('read_at');

        return $query->paginate($perPage);
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function findForUser(int $id, int $userId): ?Notification
    {
        return Notification::whereKey($id)->where('user_id', $userId)->first();
    }

    public function markRead(Notification $notification): Notification
    {
        $notification->update(['read_at' => now(), 'status' => 'Read']);
        return $notification->refresh();
    }
}
