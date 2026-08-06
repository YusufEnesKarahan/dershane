<?php

namespace App\Domain\Notification\Services;

use App\Models\Announcement;
use App\Models\AnnouncementAttachment;
use App\Models\AnnouncementCategory;
use App\Models\Branch;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementCmsService
{
    /**
     * Create Announcement with categories, branch targets and attachments
     */
    public function createAnnouncement(array $data, array $uploadedFiles = []): Announcement
    {
        return DB::transaction(function () use ($data, $uploadedFiles) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
            }

            $status = $data['status'] ?? 'draft';

            $announcement = Announcement::create([
                'branch_id' => $data['branch_id'] ?? auth()->user()?->branch_id ?? 1,
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'summary' => $data['summary'] ?? null,
                'content' => $data['content'],
                'cover_image' => $data['cover_image'] ?? null,
                'type' => $data['type'] ?? 'announcement',
                'target_role' => $data['target_role'] ?? 'all',
                'created_by' => auth()->id() ?? $data['created_by'] ?? 1,
                'publish_at' => $data['publish_at'] ?? null,
                'expire_at' => $data['expire_at'] ?? null,
                'is_pinned' => filter_var($data['is_pinned'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_popup' => filter_var($data['is_popup'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_all_branches' => filter_var($data['is_all_branches'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'notify_roles' => $data['notify_roles'] ?? [],
                'status' => $status,
                'published_at' => strtolower($status) === 'published' ? now() : null,
            ]);

            // Sync branches if not all branches
            if (!$announcement->is_all_branches && !empty($data['branch_ids'])) {
                $announcement->branches()->sync($data['branch_ids']);
            }

            // Save attachments
            $this->storeAttachments($announcement, $uploadedFiles);

            // Send notification if published
            if (strtolower($announcement->status) === 'published') {
                $this->sendDatabaseNotifications($announcement);
            }

            return $announcement;
        });
    }

    /**
     * Update Announcement
     */
    public function updateAnnouncement(Announcement $announcement, array $data, array $uploadedFiles = []): Announcement
    {
        return DB::transaction(function () use ($announcement, $data, $uploadedFiles) {
            $status = $data['status'] ?? $announcement->status;

            $announcement->update([
                'category_id' => $data['category_id'] ?? $announcement->category_id,
                'title' => $data['title'] ?? $announcement->title,
                'summary' => $data['summary'] ?? $announcement->summary,
                'content' => $data['content'] ?? $announcement->content,
                'cover_image' => $data['cover_image'] ?? $announcement->cover_image,
                'publish_at' => $data['publish_at'] ?? $announcement->publish_at,
                'expire_at' => $data['expire_at'] ?? $announcement->expire_at,
                'is_pinned' => filter_var($data['is_pinned'] ?? $announcement->is_pinned, FILTER_VALIDATE_BOOLEAN),
                'is_popup' => filter_var($data['is_popup'] ?? $announcement->is_popup, FILTER_VALIDATE_BOOLEAN),
                'is_all_branches' => filter_var($data['is_all_branches'] ?? $announcement->is_all_branches, FILTER_VALIDATE_BOOLEAN),
                'notify_roles' => $data['notify_roles'] ?? $announcement->notify_roles,
                'status' => $status,
                'published_at' => strtolower($status) === 'published' ? ($announcement->published_at ?? now()) : $announcement->published_at,
            ]);

            if (!$announcement->is_all_branches && isset($data['branch_ids'])) {
                $announcement->branches()->sync($data['branch_ids']);
            }

            if (!empty($uploadedFiles)) {
                $this->storeAttachments($announcement, $uploadedFiles);
            }

            return $announcement;
        });
    }

    /**
     * Publish Announcement
     */
    public function publish(Announcement $announcement, bool $notify = true): Announcement
    {
        $announcement->update([
            'status' => 'Published',
            'published_at' => now(),
        ]);

        if ($notify) {
            $this->sendDatabaseNotifications($announcement);
        }

        return $announcement;
    }

    /**
     * Archive Announcement
     */
    public function archive(Announcement $announcement): Announcement
    {
        $announcement->update(['status' => 'Archived']);
        return $announcement;
    }

    /**
     * Get Portal Published Announcements for Student/Parent/Teacher
     */
    public function getPortalAnnouncements(?User $user = null, ?string $role = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $userBranchId = $user?->branch_id ?? session('active_branch_id') ?? 1;

        return Announcement::published()
            ->pinned()
            ->with(['category', 'creator', 'attachments'])
            ->where(function ($q) use ($userBranchId) {
                $q->where('is_all_branches', true)
                  ->orWhere('branch_id', $userBranchId)
                  ->orWhereHas('branches', function ($bq) use ($userBranchId) {
                      $bq->where('branches.id', $userBranchId);
                  });
            })
            ->latest('published_at')
            ->paginate(10);
    }

    /**
     * Get Unseen Popup Announcement for current user/session
     */
    public function getPopupForUser(?User $user = null): ?Announcement
    {
        $userBranchId = $user?->branch_id ?? session('active_branch_id') ?? 1;

        $popups = Announcement::published()
            ->popup()
            ->where(function ($q) use ($userBranchId) {
                $q->where('is_all_branches', true)
                  ->orWhere('branch_id', $userBranchId)
                  ->orWhereHas('branches', function ($bq) use ($userBranchId) {
                      $bq->where('branches.id', $userBranchId);
                  });
            })
            ->latest()
            ->get();

        foreach ($popups as $popup) {
            if (!session()->has('popup_announcement_seen_' . $popup->id)) {
                return $popup;
            }
        }

        return null;
    }

    /**
     * Dashboard Widget Stats & Recent List
     */
    public function getDashboardWidgetData(): array
    {
        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id ?? 1;

        $base = Announcement::query()->where(function ($q) use ($branchId) {
            $q->where('is_all_branches', true)
              ->orWhere('branch_id', $branchId)
              ->orWhereHas('branches', function ($bq) use ($branchId) {
                  $bq->where('branches.id', $branchId);
              });
        });

        $recent5 = (clone $base)->published()->pinned()->with(['category', 'creator'])->latest()->take(5)->get();
        $upcoming = (clone $base)->where('publish_at', '>', now())->latest()->take(5)->get();
        $draftCount = (clone $base)->whereIn('status', ['Draft', 'draft'])->count();
        $publishedCount = (clone $base)->published()->count();

        return [
            'recent_5' => $recent5,
            'upcoming' => $upcoming,
            'draft_count' => $draftCount,
            'published_count' => $publishedCount,
        ];
    }

    private function storeAttachments(Announcement $announcement, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('announcements/attachments', 'public');
                $mime = $file->getClientMimeType();
                $ext = strtolower($file->getClientOriginalExtension());

                $type = match (true) {
                    in_array($ext, ['pdf']) => 'pdf',
                    in_array($ext, ['doc', 'docx']) => 'word',
                    in_array($ext, ['xls', 'xlsx']) => 'excel',
                    in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) => 'image',
                    default => 'document',
                };

                AnnouncementAttachment::create([
                    'announcement_id' => $announcement->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $type,
                    'mime_type' => $mime,
                ]);
            }
        }
    }

    private function sendDatabaseNotifications(Announcement $announcement): void
    {
        $roles = $announcement->notify_roles;

        if (empty($roles) && !empty($announcement->target_role) && strtolower($announcement->target_role) !== 'all') {
            $roles = [$announcement->target_role];
        }

        if (empty($roles)) {
            $roles = ['Student', 'Parent', 'Teacher'];
        }

        $usersQuery = User::query();

        if (!empty($roles)) {
            $usersQuery->whereHas('roles', function ($q) use ($roles) {
                $q->where(function ($rq) use ($roles) {
                    foreach ($roles as $r) {
                        $rq->orWhere('name', 'like', $r);
                    }
                });
            });
        }

        if ($announcement->branch_id) {
            $usersQuery->where('branch_id', $announcement->branch_id);
        }

        $users = $usersQuery->limit(500)->get();

        foreach ($users as $u) {
            Notification::create([
                'branch_id' => $u->branch_id,
                'user_id' => $u->id,
                'title' => 'Yeni Duyuru: ' . $announcement->title,
                'message' => Str::limit(strip_tags($announcement->summary ?: $announcement->content), 150),
                'type' => 'announcement',
                'status' => 'Unread',
            ]);
        }
    }
}
