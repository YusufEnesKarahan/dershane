<?php
namespace App\Domain\Blog\Services;

use App\Models\Blog;
use App\Models\BlogRevision;
use Illuminate\Support\Facades\Auth;

class BlogRevisionService
{
    public function createRevision(Blog $blog): BlogRevision
    {
        $revisionNo = $blog->revisions()->max('revision_no') + 1;

        return BlogRevision::create([
            'blog_id' => $blog->id,
            'revision_no' => $revisionNo,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'content' => $blog->content,
            'excerpt' => $blog->excerpt,
            'seo_snapshot' => [
                'seo_title' => $blog->seo_title,
                'seo_description' => $blog->seo_description,
                'seo_keywords' => $blog->seo_keywords,
                'robots' => $blog->robots,
            ],
            'author_id' => Auth::id(),
        ]);
    }

    public function restoreRevision(Blog $blog, int $revisionId): void
    {
        $revision = BlogRevision::where('blog_id', $blog->id)->findOrFail($revisionId);
        
        $blog->update([
            'title' => $revision->title,
            'slug' => $revision->slug,
            'content' => $revision->content,
            'excerpt' => $revision->excerpt,
            'seo_title' => $revision->seo_snapshot['seo_title'] ?? null,
            'seo_description' => $revision->seo_snapshot['seo_description'] ?? null,
            'seo_keywords' => $revision->seo_snapshot['seo_keywords'] ?? null,
            'robots' => $revision->seo_snapshot['robots'] ?? 'index, follow',
        ]);
    }
}
