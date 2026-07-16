<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\DTOs\Blog\CategoryDTO;
use App\Domain\Blog\Services\CategoryService;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function __construct(protected CategoryService $service) {}

    public function index()
    {
        $this->authorize('viewAny', BlogCategory::class);

        $categories = BlogCategory::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();

        return view('admin.blogs.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', BlogCategory::class);

        $request->validate(['name' => 'required|string|max:255']);

        $dto = new CategoryDTO(
            $request->name,
            $request->parent_id ? (int) $request->parent_id : null,
            $request->description,
            $request->icon,
            $request->color,
            (int) ($request->sort_order ?? 0)
        );

        $this->service->createCategory($dto);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function destroy(BlogCategory $category)
    {
        $this->authorize('delete', BlogCategory::class);

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
