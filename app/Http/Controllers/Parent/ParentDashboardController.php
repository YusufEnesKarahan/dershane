<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Domain\Parent\Services\ParentPortalService;
use App\Domain\Parent\Services\ParentAnalyticsService;
use App\Models\ParentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function __construct(
        protected ParentPortalService $portalService,
        protected ParentAnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $parent = Auth::user();
        if (!$parent) {
            return redirect()->route('login');
        }

        // Log parent access
        ParentAccessLog::create([
            'parent_id' => $parent->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'action' => 'Dashboard Access',
        ]);

        $linkedStudents = $this->portalService->getLinkedStudents($parent->id);

        if ($linkedStudents->isEmpty()) {
            return view('parent.dashboard', [
                'student' => null,
                'linkedStudents' => collect(),
                'analytics' => $this->analyticsService->getSummary($parent->id),
            ]);
        }

        // Select first student by default or by parameter
        $selectedStudentId = $request->query('student_id', $linkedStudents->first()->id);
        $dashboardData = $this->portalService->getDashboardData($parent->id, (int) $selectedStudentId);

        return view('parent.dashboard', [
            'student' => $dashboardData->student,
            'linkedStudents' => $linkedStudents,
            'attendance' => $dashboardData->attendance,
            'examResults' => $dashboardData->examResults,
            'homeworks' => $dashboardData->homeworks,
            'invoices' => $dashboardData->invoices,
            'announcements' => $dashboardData->announcements,
            'analytics' => $this->analyticsService->getSummary($parent->id),
        ]);
    }
}
