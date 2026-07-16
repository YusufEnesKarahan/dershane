<?php
namespace App\Domain\CMS\Services;

use App\Models\Page;
use App\Models\PageRevision;
use Illuminate\Support\Facades\Auth;

class RevisionService
{
    public function createSnapshot(Page $page): void
    {
        $latestNo = PageRevision::where('page_id', $page->id)->max('revision_no') ?? 0;
        $nextNo = $latestNo + 1;

        PageRevision::create([
            'page_id' => $page->id,
            'revision_no' => $nextNo,
            'title' => $page->title,
            'slug' => $page->slug,
            'excerpt' => $page->excerpt,
            'content' => $page->content,
            'seo_snapshot' => [
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'meta_keywords' => $page->meta_keywords,
                'canonical_url' => $page->canonical_url,
                'robots' => $page->robots,
            ],
            'content_snapshot' => [
                'template' => $page->template,
                'parent_id' => $page->parent_id,
            ],
            'author_id' => Auth::id(),
        ]);

        $limit = config('cms.revision_limit', 20);
        $revisions = PageRevision::where('page_id', $page->id)->orderBy('created_at', 'desc')->get();
        if ($revisions->count() > $limit) {
            $excess = $revisions->slice($limit);
            foreach ($excess as $oldRev) {
                $oldRev->delete();
            }
        }
    }
}
