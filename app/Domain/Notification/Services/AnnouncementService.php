<?php

namespace App\Domain\Notification\Services;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Domain\Notification\Enums\NotificationType;
use Illuminate\Support\Facades\DB;

class AnnouncementService
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function create(array $data): Announcement
    {
        return DB::transaction(function () use ($data) {
            $announcement = Announcement::create([
                'branch_id' => $data['branch_id'],
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'] ?? NotificationType::ANNOUNCEMENT->value,
                'target_role' => $data['target_role'] ?? null,
                'created_by' => $data['created_by'],
                'status' => 'draft',
            ]);

            return $announcement;
        });
    }

    public function publish(Announcement $announcement): Announcement
    {
        return DB::transaction(function () use ($announcement) {
            $announcement->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            if ($announcement->target_role) {
                $this->sendToRole($announcement, $announcement->target_role);
            } else {
                $this->sendToBranch($announcement);
            }

            return $announcement;
        });
    }

    public function sendToBranch(Announcement $announcement)
    {
        $users = User::where('branch_id', $announcement->branch_id)
            ->where('status', \App\Enums\UserStatus::ACTIVE->value)
            ->get();

        foreach ($users as $user) {
            $this->notificationService->send($user, $announcement->title, $announcement->content, NotificationType::from($announcement->type));
        }
    }

    public function sendToRole(Announcement $announcement, string $roleName)
    {
        $users = User::whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->where('branch_id', $announcement->branch_id)
            ->where('status', \App\Enums\UserStatus::ACTIVE->value)
            ->get();

        foreach ($users as $user) {
            $this->notificationService->send($user, $announcement->title, $announcement->content, NotificationType::from($announcement->type));
        }
    }

    public function archive(Announcement $announcement): Announcement
    {
        $announcement->update(['status' => 'archived']);
        return $announcement;
    }
}
