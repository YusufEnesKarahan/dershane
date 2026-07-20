<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Branch;
use App\DTOs\Classroom\HolidayDTO;
use App\Domain\Classroom\Actions\CreateHolidayAction;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('start_date', 'desc')->get();
        $branches = Branch::all();

        return view('admin.classrooms.holidays', compact('holidays', 'branches'));
    }

    public function store(Request $request, CreateHolidayAction $action)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $dto = HolidayDTO::fromRequest($request->all());
        $action->execute($dto);

        return redirect()->back()->with('success', 'Tatil günü başarıyla tanımlandı.');
    }
}
