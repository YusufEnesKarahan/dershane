<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerformanceSnapshot;
use App\Models\StudentGoal;

class StudentPerformanceController extends Controller
{
    public function myPerformance()
    {
        $student = auth()->user()->student;
        if (!$student) abort(403);

        $snapshot = PerformanceSnapshot::where('student_id', $student->id)
            ->orderBy('snapshot_date', 'desc')
            ->first();

        return view('student.performance.dashboard', compact('snapshot', 'student'));
    }

    public function myGoals()
    {
        $student = auth()->user()->student;
        if (!$student) abort(403);

        $goals = StudentGoal::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.performance.goals', compact('goals'));
    }
}
