<?php

namespace App\Domain\Exam\Services;

use App\Models\Exam;
use App\Models\ExamSubject;
use Illuminate\Support\Facades\DB;
use App\Domain\Tenant\Services\SubscriptionLimitService;

class ExamManagementService
{
    public function __construct(
        protected SubscriptionLimitService $limitService
    ) {}

    public function createExam(array $data): Exam
    {
        // Enforce branch isolation
        $branchId = $data['branch_id'] ?? \App\Core\Context\TenantContext::getActiveBranchId();

        // Enforce subscription limit before creating
        $this->limitService->checkExamLimit($branchId);

        return DB::transaction(function () use ($data, $branchId) {
            $exam = Exam::create([
                'branch_id' => $branchId,
                'academic_term_id' => $data['academic_term_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? 'mock_exam',
                'exam_date' => $data['exam_date'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'total_score' => $data['total_score'] ?? 100,
                'status' => $data['status'] ?? 'draft',
                'created_by' => auth()->id(),
            ]);

            if (isset($data['subjects']) && is_array($data['subjects'])) {
                foreach ($data['subjects'] as $subject) {
                    ExamSubject::create([
                        'branch_id' => $branchId,
                        'exam_id' => $exam->id,
                        'course_id' => $subject['course_id'],
                        'question_count' => $subject['question_count'] ?? 0,
                        'max_score' => $subject['max_score'] ?? 100,
                    ]);
                }
            }

            return $exam;
        });
    }

    public function updateExam(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            $exam->update($data);
            
            if (isset($data['subjects']) && is_array($data['subjects'])) {
                $exam->subjects()->delete();
                
                foreach ($data['subjects'] as $subject) {
                    ExamSubject::create([
                        'branch_id' => $exam->branch_id,
                        'exam_id' => $exam->id,
                        'course_id' => $subject['course_id'],
                        'question_count' => $subject['question_count'] ?? 0,
                        'max_score' => $subject['max_score'] ?? 100,
                    ]);
                }
            }
            
            return $exam;
        });
    }

    public function publishExam(Exam $exam): bool
    {
        return $exam->update(['status' => 'published']);
    }

    public function deleteExam(Exam $exam): bool
    {
        return DB::transaction(function () use ($exam) {
            $exam->results()->delete();
            $exam->rankings()->delete();
            $exam->subjects()->delete();
            return $exam->delete();
        });
    }
}
