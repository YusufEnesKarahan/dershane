<?php
namespace App\Domain\Blog\Actions;

use App\Models\Blog;

class SchedulePublishAction
{
    public function execute(Blog $blog, string $publishTime): void
    {
        $blog->update([
            'status' => 'Scheduled',
            'published_at' => $publishTime,
        ]);
    }
}
