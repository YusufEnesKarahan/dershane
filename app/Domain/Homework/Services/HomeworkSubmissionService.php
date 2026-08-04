<?php

namespace App\Domain\Homework\Services;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Domain\Notification\Services\NotificationService;
use App\Domain\Notification\Enums\NotificationType;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use App\Domain\Guidance\Services\StudentPerformanceService;
use Illuminate\Support\Facades\DB;
use Exception;

class HomeworkSubmissionService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected SubscriptionLimitService $limitService,
        protected StudentPerformanceService $performanceService
    ) {}

    public function submitHomework(Homework $homework, int $studentId, array $data, ?string $attachmentPath = null): HomeworkSubmission
    {
        $branchId = $homework->branch_id;
        
        // Enforce daily submission limits
        $this->limitService->checkDailySubmissionLimit($branchId);

        return DB::transaction(function () use ($homework, $studentId, $data, $attachmentPath) {
            $now = now();
            
            if ($homework->status !== 'published') {
                throw new Exception("Ödev yayında değil.");
            }

            if ($homework->due_date && $now->greaterThan($homework->due_date) && !$homework->allow_late_submission) {
                throw new Exception("Son teslim tarihi geçti ve geç teslim kabul edilmiyor.");
            }

            $status = ($homework->due_date && $now->greaterThan($homework->due_date)) ? 'late' : 'submitted';

            $submission = HomeworkSubmission::updateOrCreate(
                [
                    'branch_id' => $homework->branch_id,
                    'homework_id' => $homework->id,
                    'student_id' => $studentId,
                ],
                [
                    'submitted_at' => $now,
                    'status' => $status,
                    'attachment_path' => $attachmentPath,
                ]
            );

            // Notify Teacher
            if ($homework->teacher) {
                $this->notificationService->sendToTeacher(
                    $homework->teacher,
                    NotificationType::HOMEWORK_SUBMITTED,
                    "Bir öğrenci ödev teslim etti: {$homework->title}",
                    ['homework_id' => $homework->id, 'student_id' => $studentId]
                );
            }

            // Update Risk Level if late
            if ($status === 'late') {
                $this->performanceService->updateRiskLevel(
                    $studentId,
                    'medium',
                    "Geç ödev teslimi: {$homework->title}"
                );
            }

            return $submission;
        });
    }

    public function gradeSubmission(HomeworkSubmission $submission, array $data): HomeworkSubmission
    {
        return DB::transaction(function () use ($submission, $data) {
            if ($data['grade'] > $submission->homework->max_score) {
                throw new Exception("Not, ödevin maksimum notundan büyük olamaz.");
            }

            $submission->update([
                'grade' => $data['grade'],
                'teacher_feedback' => $data['teacher_feedback'] ?? null,
                'status' => 'graded',
                'graded_by' => auth()->id(),
                'graded_at' => now(),
            ]);

            // Notify Student
            if ($submission->student) {
                $this->notificationService->sendToStudent(
                    $submission->student,
                    NotificationType::HOMEWORK_GRADED,
                    "Ödeviniz değerlendirildi: {$submission->homework->title}",
                    ['homework_id' => $submission->homework->id, 'grade' => $data['grade']]
                );
            }
            
            // Check performance and update risk if very low score
            if ($data['grade'] < ($submission->homework->max_score * 0.4)) { // Below 40%
                $this->performanceService->updateRiskLevel(
                    $submission->student_id,
                    'high',
                    "Ödevden düşük not aldı: {$submission->homework->title} (Not: {$data['grade']})"
                );
            }

            return $submission;
        });
    }

    public function cancelSubmission(HomeworkSubmission $submission): bool
    {
        if ($submission->status === 'graded') {
            throw new Exception("Değerlendirilmiş ödev teslimi iptal edilemez.");
        }

        return DB::transaction(function () use ($submission) {
            return $submission->delete();
        });
    }
}
