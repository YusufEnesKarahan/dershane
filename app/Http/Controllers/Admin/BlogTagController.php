<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use App\Domain\Blog\Services\TagService;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function __construct(protected TagService $service) {}

    public function index()
    {
        $this->authorize('viewAny', BlogTag::class);

        $tags = BlogTag::orderBy('usage_count', 'desc')->get();

        return view('admin.blogs.tags', compact('tags'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BlogTag::class);

        $request->validate(['name' => 'required|string|max:255|unique:blog_tags,name']);

        BlogTag::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Tag created successfully.');
    }

    public function merge(Request $request)
    {
        $this->authorize('update', BlogTag::class);

        $request->validate([
            'source_id' => 'required|exists:blog_tags,id',
            'target_id' => 'required|exists:blog_tags,id|different:source_id',
        ]);

        $source = BlogTag::findOrFail($request->source_id);
        $target = BlogTag::findOrFail($request->target_id);

        $this->service->mergeTags($source, $target);

        return redirect()->back()->with('success', 'Tags merged successfully.');
    }

    public function destroy(BlogTag $tag)
    {
        $this->authorize('delete', BlogTag::class);

        $tag->delete();

        return redirect()->back()->with('success', 'Tag deleted successfully.');
    }
}
