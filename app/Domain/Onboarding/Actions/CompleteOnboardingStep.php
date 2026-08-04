<?php

namespace App\Domain\Onboarding\Actions;

use App\Domain\Onboarding\Models\OnboardingStep;
use App\Domain\Onboarding\Models\OnboardingChecklist;
use App\Models\Branch;

class CompleteOnboardingStep
{
    public function execute($branch, int $stepNumber, ?string $checklistKey = null): void
    {
        $branchId = $branch instanceof Branch ? $branch->id : (int) $branch;

        // Update or create onboarding step
        $stepRecord = OnboardingStep::where('branch_id', $branchId)->first();

        if ($stepRecord) {
            $nextStep = max($stepRecord->step, $stepNumber + 1);
            $isCompleted = $nextStep > 5;

            $stepRecord->update([
                'step' => $nextStep > 5 ? 5 : $nextStep,
                'status' => $isCompleted ? 'completed' : 'in_progress',
                'completed_at' => $isCompleted ? now() : $stepRecord->completed_at,
            ]);
        } else {
            OnboardingStep::create([
                'branch_id' => $branchId,
                'step' => min(5, $stepNumber + 1),
                'status' => $stepNumber >= 5 ? 'completed' : 'in_progress',
                'completed_at' => $stepNumber >= 5 ? now() : null,
            ]);
        }

        // Update checklist key if provided
        if ($checklistKey) {
            OnboardingChecklist::updateOrCreate(
                ['branch_id' => $branchId, 'key' => $checklistKey],
                ['completed' => true, 'completed_at' => now()]
            );
        }
    }
}
