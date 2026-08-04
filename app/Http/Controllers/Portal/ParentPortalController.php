<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Domain\Portal\Services\ParentPortalService;
use App\Domain\Exam\Services\ParentExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentPortalController extends Controller
{
    public function __construct(
        protected ParentPortalService $service,
        protected ParentExamService $examService,
        protected \App\Domain\Dashboard\Services\DashboardService $dashboardService
    ) {}

    public function dashboard(Request $request)
    {
        $guardian = $this->service->getGuardianByUserId(Auth::id());
        
        if (!$guardian) {
            abort(403, 'Veli profili bulunamadı.');
        }

        $children = $this->service->getChildren($guardian->id);
        
        $childrenData = [];
        foreach ($children as $child) {
            $childrenData[] = [
                'student' => $child,
                'attendance_stats' => $this->service->getChildAttendanceStats($child->id),
                'recent_attendance' => $this->service->getChildAttendance($child->id, 5),
                'exam_results' => $this->examService->getChildResults($child),
            ];
        }

        $dashboardData = $this->dashboardService->getParentDashboardData(Auth::user());

        return view('portal.parent.dashboard', array_merge(compact('guardian', 'childrenData'), $dashboardData));
    }
}
