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
        if (!$teacher && $user?->hasRole('Administrator')) {
            $teacher = \App\Models\Teacher::first();
        }
        if (!$teacher) {
            return redirect()->back();
        }

        $assignedClasses = $this->portalService->getAssignedClasses($teacher->id);

        return view('teacher.classes', compact('assignedClasses'));
    }
}
