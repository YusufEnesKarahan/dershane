<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaFolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id',
        ]);

        MediaFolder::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Folder created successfully.');
    }

    public function destroy(MediaFolder $folder)
    {
        $folder->delete();
        return redirect()->back()->with('success', 'Folder deleted successfully.');
    }
}
