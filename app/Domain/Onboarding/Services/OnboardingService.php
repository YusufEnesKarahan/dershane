<?php

namespace App\Domain\Onboarding\Services;

use App\Domain\Onboarding\Models\OnboardingStep;
use App\Domain\Onboarding\Models\OnboardingChecklist;
use App\Domain\Onboarding\Models\InstitutionSetting;
use App\Domain\Onboarding\Actions\CompleteOnboardingStep;
use App\Models\Branch;
use App\Core\Context\TenantContext;

class OnboardingService
{
    public const TOTAL_STEPS = 5;

    public const CHECKLIST_KEYS = [
        'institution_profile_completed',
        'academic_year_created',
        'package_selected',
        'teacher_added',
        'classroom_created',
    ];

    public function __construct(
        protected CompleteOnboardingStep $completeStepAction
    ) {}

    /**
     * Initialize default onboarding records for a newly created or uninitialized branch.
     */
    public function initializeBranchOnboarding($branch): void
    {
        $branchId = $this->resolveBranchId($branch);

        if (!$branchId) {
            return;
        }

        OnboardingStep::firstOrCreate(
            ['branch_id' => $branchId],
            ['step' => 1, 'status' => 'in_progress']
        );

        foreach (self::CHECKLIST_KEYS as $key) {
            OnboardingChecklist::firstOrCreate(
                ['branch_id' => $branchId, 'key' => $key],
                ['completed' => false]
            );
        }
    }

    /**
     * Get onboarding progress summary for a branch.
     */
    public function getProgress($branch = null): array
    {
        $branchId = $this->resolveBranchId($branch);

        if (!$branchId) {
            return [
                'total_steps' => self::TOTAL_STEPS,
                'completed_steps' => 0,
                'percentage' => 0,
                'remaining_steps' => self::TOTAL_STEPS,
                'current_step' => 1,
                'is_completed' => false,
                'checklists' => [],
            ];
        }

        $this->initializeBranchOnboarding($branchId);

        $stepRecord = OnboardingStep::where('branch_id', $branchId)->first();
        $checklists = OnboardingChecklist::where('branch_id', $branchId)->get();

        $completedCount = $checklists->where('completed', true)->count();
        $percentage = (int) round(($completedCount / self::TOTAL_STEPS) * 100);
        $isCompleted = $completedCount >= self::TOTAL_STEPS || ($stepRecord && $stepRecord->status === 'completed');

        return [
            'total_steps' => self::TOTAL_STEPS,
            'completed_steps' => $completedCount,
            'percentage' => min(100, $percentage),
            'remaining_steps' => max(0, self::TOTAL_STEPS - $completedCount),
            'current_step' => $stepRecord ? $stepRecord->step : 1,
            'is_completed' => $isCompleted,
            'checklists' => $checklists->pluck('completed', 'key')->toArray(),
        ];
    }

    /**
     * Complete a step and mark associated checklist key.
     */
    public function completeStep($branch, int $stepNumber, ?string $key = null): void
    {
        $branchId = $this->resolveBranchId($branch);
        $this->completeStepAction->execute($branchId, $stepNumber, $key);
    }

    /**
     * Reset a step status.
     */
    public function resetStep($branch, int $stepNumber, ?string $key = null): void
    {
        $branchId = $this->resolveBranchId($branch);

        if ($stepRecord = OnboardingStep::where('branch_id', $branchId)->first()) {
            $stepRecord->update(['step' => min($stepRecord->step, $stepNumber), 'status' => 'in_progress']);
        }

        if ($key) {
            OnboardingChecklist::where('branch_id', $branchId)->where('key', $key)->update([
                'completed' => false,
                'completed_at' => null,
            ]);
        }
    }

    /**
     * Check if onboarding is 100% completed for a branch.
     */
    public function isCompleted($branch = null): bool
    {
        $progress = $this->getProgress($branch);
        return $progress['is_completed'];
    }

    /**
     * Get or create institution settings for a branch.
     */
    public function getInstitutionSettings($branch = null): InstitutionSetting
    {
        $branchId = $this->resolveBranchId($branch);
        $branchObj = Branch::find($branchId);

        return InstitutionSetting::firstOrCreate(
            ['branch_id' => $branchId],
            [
                'institution_name' => $branchObj ? $branchObj->name : 'Dershane Kurumu',
                'phone' => '02120000000',
                'email' => 'info@dershane.com',
                'address' => 'Merkez Mah. Kurum Cad. No:1',
            ]
        );
    }

    /**
     * Resolve branch_id from Branch model, int, or active tenant context.
     */
    protected function resolveBranchId($branch = null): ?int
    {
        if ($branch instanceof Branch) {
            return $branch->id;
        }

        if (is_numeric($branch)) {
            return (int) $branch;
        }

        if (auth()->check() && auth()->user()->branch_id) {
            return auth()->user()->branch_id;
        }

        return TenantContext::getActiveBranchId();
    }
}
