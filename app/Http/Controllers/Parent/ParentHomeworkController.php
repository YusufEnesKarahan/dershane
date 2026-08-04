<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use Illuminate\Http\Request;

class ParentHomeworkController extends Controller
{
    public function index(Request $request)
    {
        $parent = auth()->user()->guardian;
        if (!$parent) {
            abort(403, 'Veli profili bulunamadı.');
        }
        
        $students = $parent->students;
        $studentIds = $students->pluck('id');

        $homeworks = Homework::whereHas('classroom.students', function($q) use ($studentIds) {
            $q->whereIn('students.id', $studentIds);
        })
        ->where('status', '!=', 'draft')
        ->with(['course', 'teacher.user', 'submissions' => function($q) use ($studentIds) {
            $q->whereIn('student_id', $studentIds);
        }])
        ->orderBy('due_date', 'asc')
        ->get();

        return view('parent.homeworks.index', compact('homeworks', 'students'));
    }

    public function show(Homework $homework)
    {
        $parent = auth()->user()->guardian;
        if (!$parent) {
            abort(403, 'Veli profili bulunamadı.');
        }

        $students = $parent->students;
        $studentIds = $students->pluck('id');

        $hasAccess = $homework->classroom->students()->whereIn('students.id', $studentIds)->exists();
        if (!$hasAccess || $homework->status === 'draft') {
            abort(403, 'Bu ödevi görüntüleme yetkiniz yok.');
        }

        $homework->load(['course', 'teacher.user', 'submissions' => function($q) use ($studentIds) {
            $q->whereIn('student_id', $studentIds);
        }]);

        return view('parent.homeworks.show', compact('homework'));
    }
}
