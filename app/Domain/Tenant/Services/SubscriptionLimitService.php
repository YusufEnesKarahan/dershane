<?php

namespace App\Domain\Tenant\Services;

use App\Models\Branch;
use App\Models\Student;
use App\Models\Teacher;

class SubscriptionLimitService
{
    /**
     * Check if the tenant has reached the maximum allowed students limit.
     */
    public function checkStudentLimit(int $branchId): bool
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            // Default to no limit if no subscription or plan is found,
            // or perhaps default to a strict limit? We will allow for now.
            return true; 
        }

        $plan = $branch->subscription->plan;
        
        // If max_students is null or 0, we can assume unlimited (or follow specific business logic).
        // Usually, a limit of 0 means unlimited or max integer. Let's assume non-null values matter.
        if ($plan->max_students === null || $plan->max_students <= 0) {
            return true;
        }

        // Count current active or total students
        // Often limits apply to non-deleted students
        $currentStudentCount = Student::where('branch_id', $branchId)->count();

        return $currentStudentCount < $plan->max_students;
    }

    /**
     * Check if the tenant has reached the maximum allowed teachers limit.
     */
    public function checkTeacherLimit(int $branchId): bool
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return true; 
        }

        $plan = $branch->subscription->plan;
        
        if ($plan->max_teachers === null || $plan->max_teachers <= 0) {
            return true;
        }

        $currentTeacherCount = Teacher::where('branch_id', $branchId)->count();

        return $currentTeacherCount < $plan->max_teachers;
    }

    /**
     * Check if the tenant has reached the maximum allowed classrooms limit.
     */
    public function checkClassroomLimit(int $branchId): bool
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return true; 
        }

        $plan = $branch->subscription->plan;
        
        if ($plan->max_classrooms === null || $plan->max_classrooms <= 0) {
            return true;
        }

        $currentClassroomCount = \App\Models\Classroom::where('branch_id', $branchId)->count();

        return $currentClassroomCount < $plan->max_classrooms;
    }

    /**
     * Check if the tenant has reached the maximum allowed exams limit.
     */
    public function checkExamLimit(int $branchId): void
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_exams'] ?? null;

        if ($limit === null || $limit === 'unlimited' || (int)$limit <= 0) {
            return;
        }

        $currentExamCount = \App\Models\Exam::withoutGlobalScopes()->where('branch_id', $branchId)->count();

        if ($currentExamCount >= (int)$limit) {
            throw new \Exception("Maksimum sınav oluşturma sınırına ({$limit}) ulaştınız. Lütfen paketinizi yükseltin.");
        }
    }

    /**
     * Check if the tenant has reached the maximum allowed schedules limit.
     */
    public function checkScheduleLimit(int $branchId): bool
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return true; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_schedules'] ?? null;

        if ($limit === null || $limit <= 0) {
            return true;
        }

        $currentScheduleCount = \App\Models\LessonSchedule::where('branch_id', $branchId)->count();

        return $currentScheduleCount < $limit;
    }

    public function checkHomeworkLimit(int $branchId): bool
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return true; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_homeworks'] ?? null;
        
        if ($limit === null || $limit === 'unlimited') {
            return true;
        }

        $currentCount = \App\Models\Homework::withoutGlobalScopes()->where('branch_id', $branchId)->count();

        return $currentCount < (int)$limit;
    }

    public function checkPaymentPlanLimit(int $branchId): void
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_payment_plans'] ?? null;
        
        if ($limit === null || $limit === 'unlimited' || (int)$limit <= 0) {
            return;
        }

        $currentCount = \App\Models\PaymentPlan::withoutGlobalScopes()->where('branch_id', $branchId)->count();

        if ($currentCount >= (int)$limit) {
            throw new \Exception("Maksimum ödeme planı sınırına ({$limit}) ulaştınız. Lütfen paketinizi yükseltin.");
        }
    }

    public function checkGuidanceLimit(int $branchId): void
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_guidance_records'] ?? null;
        
        if ($limit === null || $limit === 'unlimited' || (int)$limit <= 0) {
            return;
        }

        $currentCount = \App\Models\StudentGuidanceRecord::withoutGlobalScopes()->where('branch_id', $branchId)->count();

        if ($currentCount >= (int)$limit) {
            throw new \Exception("Maksimum rehberlik kaydı sınırına ({$limit}) ulaştınız. Lütfen paketinizi yükseltin.");
        }
    }

    public function checkAttendanceLimit(int $branchId): void
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_daily_attendance'] ?? null;
        
        if ($limit === null || $limit === 'unlimited' || (int)$limit <= 0) {
            return;
        }

        $currentCount = \App\Models\AttendanceSession::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->whereDate('session_date', \Carbon\Carbon::today())
            ->count();

        if ($currentCount >= (int)$limit) {
            throw new \Exception("Günlük maksimum yoklama oturumu sınırına ({$limit}) ulaştınız. Lütfen paketinizi yükseltin.");
        }
    }

    public function checkHomeworkLimit(int $branchId): void
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_homeworks'] ?? null;
        
        if ($limit === null || $limit === 'unlimited' || (int)$limit <= 0) {
            return;
        }

        $currentCount = \App\Models\Homework::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->count();

        if ($currentCount >= (int)$limit) {
            throw new \Exception("Maksimum ödev sınırına ({$limit}) ulaştınız. Lütfen paketinizi yükseltin.");
        }
    }

    public function checkDailySubmissionLimit(int $branchId): void
    {
        $branch = Branch::with('subscription.plan')->find($branchId);
        
        if (!$branch || !$branch->subscription || !$branch->subscription->plan) {
            return; 
        }

        $plan = $branch->subscription->plan;
        
        $limit = $plan->limits['max_daily_submissions'] ?? null;
        
        if ($limit === null || $limit === 'unlimited' || (int)$limit <= 0) {
            return;
        }

        $currentCount = \App\Models\HomeworkSubmission::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->whereDate('submitted_at', \Carbon\Carbon::today())
            ->count();

        if ($currentCount >= (int)$limit) {
            throw new \Exception("Günlük maksimum ödev teslimi sınırına ({$limit}) ulaştınız. Lütfen paketinizi yükseltin.");
        }
    }
}
