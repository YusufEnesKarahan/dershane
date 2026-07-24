<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\Branch;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::withCount('assets')->get();
        $locations = AssetLocation::with('branch')->get();
        $branches = Branch::all();
        return view('admin.inventory.categories', compact('categories', 'locations', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:asset_categories,code',
        ]);

        AssetCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori başarıyla eklendi.');
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AssetLocation::create([
            'branch_id' => $request->branch_id ? (int) $request->branch_id : null,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Lokasyon başarıyla eklendi.');
    }
}
