<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Repositories\DocumentCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentCategoryController extends Controller
{
    public function __construct(
        protected DocumentCategoryRepository $categoryRepo
    ) {}

    public function index()
    {
        $categories = $this->categoryRepo->all();
        return view('admin.documents.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
        ]);

        $this->categoryRepo->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color ?? '#0d9488',
            'is_active' => true,
        ]);

        return redirect()->route('admin.document-categories.index')->with('success', 'Kategori eklendi.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->categoryRepo->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.document-categories.index')->with('success', 'Kategori güncellendi.');
    }

    public function destroy(int $id)
    {
        $this->categoryRepo->delete($id);
        return redirect()->route('admin.document-categories.index')->with('success', 'Kategori silindi.');
    }
}
