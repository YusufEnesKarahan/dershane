<?php
namespace App\Domain\CMS\Services;

use App\Models\Page;
use Illuminate\Support\Facades\Auth;

class RevisionService
{
    public function createSnapshot(Page $page): void
    {
        $revisions = $page->revisions ?? [];
        
        $snapshot = [
            'title' => $page->title,
            'content' => $page->content,
            'excerpt' => $page->excerpt,
            'updated_by' => Auth::id(),
            'timestamp' => now()->toIso8601String(),
        ];

        $revisions[] = $snapshot;
        
        // Keep max 10 revisions
        if (count($revisions) > 10) {
            array_shift($revisions);
        }

        $page->revisions = $revisions;
        $page->saveQuietly();
    }
}
