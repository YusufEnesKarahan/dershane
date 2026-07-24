<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['manager', 'positions'])->get();
        $positions = Position::with('department')->get();
        $users = User::all();
        return view('admin.hr.departments', compact('departments', 'positions', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => $request->code,
            'manager_id' => $request->manager_id ? (int) $request->manager_id : null,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Departman başarıyla oluşturuldu.');
    }

    public function storePosition(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'base_salary' => 'required|numeric',
        ]);

        Position::create([
            'department_id' => (int) $request->department_id,
            'name' => $request->name,
            'level' => $request->level,
            'base_salary' => (float) $request->base_salary,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Pozisyon başarıyla eklendi.');
    }
}
