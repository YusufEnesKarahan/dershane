<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\Student;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::with('student')->paginate(15);
        return view('admin.finance.scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        $students = Student::all();
        return view('admin.finance.scholarships.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Scholarship::create($validated);

        return redirect()->route('admin.scholarships.index')->with('success', 'Burs tanımlandı.');
    }

    public function edit(Scholarship $scholarship)
    {
        $students = Student::all();
        return view('admin.finance.scholarships.edit', compact('scholarship', 'students'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $scholarship->update($validated);

        return redirect()->route('admin.scholarships.index')->with('success', 'Burs güncellendi.');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return redirect()->route('admin.scholarships.index')->with('success', 'Burs silindi.');
    }
}
