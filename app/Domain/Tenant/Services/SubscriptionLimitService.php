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
}
