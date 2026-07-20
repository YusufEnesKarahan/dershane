<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index()
    {
        $terms = AcademicTerm::orderBy('start_date', 'desc')->get();

        return view('admin.classrooms.academic-calendar', compact('terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        AcademicTerm::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => (bool) ($request->is_active ?? true),
        ]);

        return redirect()->back()->with('success', 'Akademik dönem başarıyla oluşturuldu.');
    }
}
