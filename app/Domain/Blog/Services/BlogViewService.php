<?php
namespace App\Domain\Blog\Services;

use App\Models\Blog;
use App\Models\BlogView;
use Illuminate\Http\Request;

class BlogViewService
{
    public function trackView(Blog $blog, Request $request): void
    {
        $ip = $request->ip();
        $ua = $request->userAgent();

        $exists = BlogView::where('blog_id', $blog->id)
            ->where('ip_address', $ip)
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        BlogView::create([
            'blog_id' => $blog->id,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'is_unique' => !$exists,
        ]);
    }
}
