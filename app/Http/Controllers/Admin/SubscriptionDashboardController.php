<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Subscription\Services\SubscriptionService;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Branch;
use App\Core\Context\TenantContext;
use Illuminate\Http\Request;

class SubscriptionDashboardController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function index()
    {
        $branchId = TenantContext::getActiveBranchId()
            ?? session('active_branch_id')
            ?? auth()->user()?->branch_id
            ?? Branch::value('id');

        $subscription = $this->subscriptionService->getSubscription($branchId);
        $plan = $subscription?->plan;

        $expiry = $subscription?->expires_at ?? $subscription?->ends_at;
        $remainingDays = $expiry ? (int) max(0, now()->diffInDays($expiry, false)) : 0;

        $studentCount = Student::where('branch_id', $branchId)->count();
        $teacherCount = Teacher::where('branch_id', $branchId)->count();
        $classroomCount = Classroom::where('branch_id', $branchId)->count();

        $maxStudents = $plan?->max_students ?? 200;
        $maxTeachers = $plan?->max_teachers ?? 10;
        $maxClassrooms = $plan?->max_classrooms ?? 5;

        return view('admin.subscription.index', compact(
            'subscription',
            'plan',
            'remainingDays',
            'studentCount',
            'teacherCount',
            'classroomCount',
            'maxStudents',
            'maxTeachers',
            'maxClassrooms'
        ));
    }
}
