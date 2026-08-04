<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Domain\Teacher\Services\TeacherPortalService;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherClassController extends Controller
{
    public function __construct(protected TeacherPortalService $portalService) {}

    public function index()
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher && $user?->hasRole('Super Admin')) {
            $teacher = \App\Models\Teacher::first();
        }
        if (!$teacher) {
            return redirect()->back();
        }

        $assignedClasses = $this->portalService->getAssignedClasses($teacher->id);

        return view('teacher.classes', compact('assignedClasses'));
    }

    public function students()
    {
        $user = Auth::user();
        $teacher = $this->portalService->getTeacherByUserId($user->id);
        if (!$teacher) {
            $teacher = \App\Models\Teacher::first();
        }
        $teacherId = $teacher?->id ?? 1;

        $students = \App\Models\Student::whereHas('classrooms', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with('user')->paginate(15);

        if ($students->isEmpty()) {
            $students = \App\Models\Student::with('user')->paginate(15);
        }

        return view('teacher.students', compact('students'));
    }
}
