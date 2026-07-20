<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ClassroomType;
use App\Models\Branch;
use App\DTOs\Classroom\CreateClassroomDTO;
use App\DTOs\Classroom\UpdateClassroomDTO;
use App\Domain\Classroom\Actions\CreateClassroomAction;
use App\Core\Repositories\Interfaces\ClassroomRepositoryInterface;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function __construct(protected ClassroomRepositoryInterface $repository) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Classroom::class);

        $classrooms = $this->repository->paginate(15, $request->all());
        $types = ClassroomType::all();
        $branches = Branch::all();

        return view('admin.classrooms.index', compact('classrooms', 'types', 'branches'));
    }

    public function create()
    {
        $this->authorize('create', Classroom::class);

        $types = ClassroomType::all();
        $branches = Branch::all();

        return view('admin.classrooms.edit', compact('types', 'branches'));
    }

    public function store(Request $request, CreateClassroomAction $action)
    {
        $this->authorize('create', Classroom::class);

        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        $dto = new CreateClassroomDTO(
            $request->code,
            $request->name,
            $request->branch_id ? (int) $request->branch_id : null,
            $request->classroom_type_id ? (int) $request->classroom_type_id : null,
            (int) $request->capacity,
            $request->color_code ?? '#4F46E5',
            (bool) ($request->is_active ?? true)
        );

        try {
            $classroom = $action->execute($dto);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.classrooms.index')->with('success', 'Derslik başarıyla oluşturuldu.');
    }

    public function edit(Classroom $classroom)
    {
        $this->authorize('update', Classroom::class);

        $types = ClassroomType::all();
        $branches = Branch::all();

        return view('admin.classrooms.edit', compact('classroom', 'types', 'branches'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $this->authorize('update', Classroom::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        $dto = UpdateClassroomDTO::fromRequest($request->all());
        $this->repository->update($classroom, (array) $dto);

        return redirect()->route('admin.classrooms.index')->with('success', 'Derslik güncellendi.');
    }

    public function destroy(Classroom $classroom)
    {
        $this->authorize('delete', Classroom::class);

        $this->repository->delete($classroom);

        return redirect()->route('admin.classrooms.index')->with('success', 'Derslik silindi.');
    }

    public function analytics()
    {
        $this->authorize('viewAny', Classroom::class);

        $totalClassrooms = Classroom::count();
        $totalCapacity = Classroom::sum('capacity');

        return view('admin.classrooms.analytics', compact('totalClassrooms', 'totalCapacity'));
    }
}
