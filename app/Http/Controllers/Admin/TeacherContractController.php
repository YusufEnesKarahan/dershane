<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Domain\Teacher\Services\TeacherContractService;
use Illuminate\Http\Request;

class TeacherContractController extends Controller
{
    public function __construct(protected TeacherContractService $service) {}

    public function index()
    {
        $this->authorize('viewAny', Teacher::class);

        $teachers = Teacher::with(['user', 'contracts'])->get();

        return view('admin.teachers.contracts', compact('teachers'));
    }

    public function store(Request $request)
    {
        $this->authorize('update', Teacher::class);

        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'start_date' => 'required|date',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        $this->service->signContract($teacher, $request->all());

        return redirect()->back()->with('success', 'Employment contract generated successfully.');
    }
}
