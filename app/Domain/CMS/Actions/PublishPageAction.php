<?php
namespace App\Domain\CMS\Actions;

use App\Models\Page;

class PublishPageAction
{
    public function execute(Page $page, string $status = 'published'): bool
    {
        return $page->update([
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }
}
