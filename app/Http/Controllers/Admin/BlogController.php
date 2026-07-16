<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\DTOs\Blog\BlogFilterDTO;
use App\DTOs\Blog\CreateBlogDTO;
use App\DTOs\Blog\UpdateBlogDTO;
use App\Domain\Blog\Actions\CreateBlogAction;
use App\Domain\Blog\Actions\UpdateBlogAction;
use App\Domain\Blog\Actions\DeleteBlogAction;
use App\Domain\Blog\Actions\DuplicateBlogAction;
use App\Domain\Blog\Actions\PublishBlogAction;
use App\Domain\Blog\Actions\ArchiveBlogAction;
use App\Core\Repositories\Interfaces\BlogRepositoryInterface;
use App\Domain\Blog\Services\ContentAnalyzerService;
use App\Domain\Blog\Services\BlogRevisionService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        protected BlogRepositoryInterface $repository,
        protected ContentAnalyzerService $analyzerService,
        protected BlogRevisionService $revisionService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Blog::class);

        $filters = BlogFilterDTO::fromRequest($request->all());
        $blogs = $this->repository->paginate(15, (array) $filters);

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $this->authorize('create', Blog::class);

        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        $blogs = Blog::all();

        return view('admin.blogs.edit', compact('categories', 'tags', 'blogs'));
    }

    public function store(Request $request, CreateBlogAction $action)
    {
        $this->authorize('create', Blog::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $dto = new CreateBlogDTO(
            $request->title,
            $request->content,
            $request->excerpt,
            $request->category_id ? (int) $request->category_id : null,
            $request->status ?? 'Draft',
            $request->featured_image
        );

        $blog = $action->execute($dto);

        return redirect()->route('admin.blogs.edit', $blog->id)->with('success', 'Article created successfully.');
    }

    public function edit(Blog $blog)
    {
        $this->authorize('update', Blog::class);

        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        $blogs = Blog::where('id', '<>', $blog->id)->get();

        // SEO Analyzer Score
        $analysis = $this->analyzerService->analyze($blog->title, $blog->content, $blog->seo_description);

        return view('admin.blogs.edit', compact('blog', 'categories', 'tags', 'blogs', 'analysis'));
    }

    public function update(Request $request, Blog $blog, UpdateBlogAction $action)
    {
        $this->authorize('update', Blog::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $dto = UpdateBlogDTO::fromRequest($request->all());

        $action->execute($blog, $dto);

        return redirect()->route('admin.blogs.edit', $blog->id)->with('success', 'Article updated successfully.');
    }

    public function destroy(Blog $blog, DeleteBlogAction $action)
    {
        $this->authorize('delete', Blog::class);

        $action->execute($blog);

        return redirect()->route('admin.blogs.index')->with('success', 'Article deleted successfully.');
    }

    public function duplicate(Blog $blog, DuplicateBlogAction $action)
    {
        $this->authorize('create', Blog::class);

        $clone = $action->execute($blog);

        return redirect()->route('admin.blogs.index')->with('success', 'Article duplicated successfully.');
    }

    public function publish(Blog $blog, PublishBlogAction $action)
    {
        $this->authorize('update', Blog::class);

        $action->execute($blog);

        return redirect()->back()->with('success', 'Article published successfully.');
    }

    public function archive(Blog $blog, ArchiveBlogAction $action)
    {
        $this->authorize('update', Blog::class);

        $action->execute($blog);

        return redirect()->back()->with('success', 'Article archived successfully.');
    }

    public function restoreRevision(Blog $blog, $revisionId)
    {
        $this->authorize('update', Blog::class);

        $this->revisionService->restoreRevision($blog, (int) $revisionId);

        return redirect()->back()->with('success', 'Revision restored successfully.');
    }

    public function analytics()
    {
        $this->authorize('viewAny', Blog::class);

        // Simple aggregation logic for popular stats dashboard
        $popular = Blog::withCount(['views', 'comments'])->orderBy('views_count', 'desc')->take(5)->get();

        return view('admin.blogs.analytics', compact('popular'));
    }
}
