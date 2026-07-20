<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLevel;
use App\Core\Repositories\Interfaces\CourseLevelRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseLevelController extends Controller
{
    public function __construct(protected CourseLevelRepositoryInterface $repository) {}

    public function index()
    {
        $this->authorize('viewAny', Course::class);

        $levels = $this->repository->all();

        return view('admin.courses.levels', compact('levels'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $request->validate(['name' => 'required|string|max:255|unique:course_levels,name']);

        $this->repository->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Course level created successfully.');
    }

    public function destroy(CourseLevel $level)
    {
        $this->authorize('delete', Course::class);

        $level->delete();

        return redirect()->back()->with('success', 'Course level deleted successfully.');
    }
}
