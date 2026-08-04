<?php

namespace App\Domain\Academic\Services;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Domain\Notification\Services\NotificationService;
use App\Domain\Notification\Enums\NotificationType;
use Illuminate\Support\Facades\DB;
use Exception;

class HomeworkSubmissionService
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function submitHomework(Homework $homework, int $studentId, array $data, array $files = []): HomeworkSubmission
    {
        return DB::transaction(function () use ($homework, $studentId, $data, $files) {
            $now = now();
            
            if ($homework->status !== 'published') {
                throw new Exception("Ödev yayında değil.");
            }

            if ($now->greaterThan($homework->due_date) && !$homework->allow_late_submission) {
                throw new Exception("Son teslim tarihi geçti ve geç teslim kabul edilmiyor.");
            }

            $status = $now->greaterThan($homework->due_date) ? 'late' : 'submitted';

            $submission = HomeworkSubmission::updateOrCreate(
                [
                    'branch_id' => $homework->branch_id,
                    'homework_id' => $homework->id,
                    'student_id' => $studentId,
                ],
                [
                    'submitted_at' => $now,
                    'status' => $status,
                ]
            );

            // Process files
            foreach ($files as $fileData) {
                $submission->files()->create([
                    'branch_id' => $homework->branch_id,
                    'homework_id' => $homework->id,
                    'disk' => $fileData['disk'] ?? 'public',
                    'path' => $fileData['path'],
                    'original_name' => $fileData['original_name'],
                    'mime_type' => $fileData['mime_type'],
                    'size' => $fileData['size'],
                ]);
            }

            // Notify Teacher
            $this->notificationService->sendToTeacher(
                $homework->teacher,
                "Yeni Ödev Teslimi: {$homework->title}",
                "{$submission->student->user->name} ödevini teslim etti.",
                NotificationType::SYSTEM
            );

            return $submission;
        });
    }

    public function updateSubmission(HomeworkSubmission $submission, array $files = []): HomeworkSubmission
    {
        return DB::transaction(function () use ($submission, $files) {
            $now = now();
            $homework = $submission->homework;

            if ($now->greaterThan($homework->due_date) && !$homework->allow_late_submission) {
                throw new Exception("Son teslim tarihi geçti, ödev güncellenemez.");
            }

            $submission->update([
                'submitted_at' => $now,
                'status' => $now->greaterThan($homework->due_date) ? 'late' : 'submitted',
            ]);

            foreach ($files as $fileData) {
                $submission->files()->create([
                    'branch_id' => $homework->branch_id,
                    'homework_id' => $homework->id,
                    'disk' => $fileData['disk'] ?? 'public',
                    'path' => $fileData['path'],
                    'original_name' => $fileData['original_name'],
                    'mime_type' => $fileData['mime_type'],
                    'size' => $fileData['size'],
                ]);
            }

            return $submission;
        });
    }

    public function gradeSubmission(HomeworkSubmission $submission, int $teacherUserId, int $score, ?string $feedback): HomeworkSubmission
    {
        return DB::transaction(function () use ($submission, $teacherUserId, $score, $feedback) {
            $homework = $submission->homework;

            // Teacher ownership validation
            $teacher = \App\Models\Teacher::where('user_id', $teacherUserId)->first();
            if (!$teacher || ($homework->teacher_id !== $teacher->id && !auth()->user()->hasRole('Admin'))) {
                throw new Exception("Sadece kendi ödevlerinizi değerlendirebilirsiniz.");
            }

            if ($score > $homework->max_score || $score < 0) {
                throw new Exception("Puan 0 ile {$homework->max_score} arasında olmalıdır.");
            }

            $submission->update([
                'score' => $score,
                'feedback' => $feedback,
                'status' => 'graded',
                'graded_by' => $teacherUserId,
                'graded_at' => now(),
            ]);

            // Notify Student
            $this->notificationService->sendToStudent(
                $submission->student,
                "Ödeviniz Notlandırıldı: {$homework->title}",
                "Ödeviniz notlandırıldı. Puanınız: {$score}/{$homework->max_score}",
                NotificationType::SYSTEM
            );

            // Notify Parents
            foreach ($submission->student->guardians as $guardian) {
                $this->notificationService->sendToParent(
                    $guardian,
                    "Çocuğunuzun Ödevi Notlandırıldı: {$homework->title}",
                    "{$submission->student->user->name} adlı öğrencinin ödevi notlandırıldı. Puan: {$score}/{$homework->max_score}",
                    NotificationType::SYSTEM
                );
            }

            return $submission;
        });
    }

    public function bulkGrade(array $submissionsData, int $teacherUserId): void
    {
        DB::transaction(function () use ($submissionsData, $teacherUserId) {
            foreach ($submissionsData as $data) {
                $submission = HomeworkSubmission::findOrFail($data['id']);
                $this->gradeSubmission($submission, $teacherUserId, $data['score'], $data['feedback'] ?? null);
            }
        });
    }

    public function reopenSubmission(HomeworkSubmission $submission, int $teacherUserId): HomeworkSubmission
    {
        return DB::transaction(function () use ($submission, $teacherUserId) {
            $homework = $submission->homework;
            $teacher = \App\Models\Teacher::where('user_id', $teacherUserId)->first();
            
            if (!$teacher || ($homework->teacher_id !== $teacher->id && !auth()->user()->hasRole('Admin'))) {
                throw new Exception("Sadece kendi ödevlerinizi yönetebilirsiniz.");
            }

            $submission->update([
                'status' => 'pending',
                'score' => null,
                'feedback' => null,
                'graded_by' => null,
                'graded_at' => null,
            ]);

            return $submission;
        });
    }

    public function getSubmissionStatistics(Homework $homework): array
    {
        $totalStudents = \App\Models\Student::where('classroom_id', $homework->classroom_id)->count();
        $submissions = $homework->submissions;

        $submittedCount = $submissions->whereIn('status', ['submitted', 'late', 'graded'])->count();
        $lateCount = $submissions->where('status', 'late')->count();
        $gradedCount = $submissions->where('status', 'graded')->count();
        
        $averageScore = $gradedCount > 0 ? $submissions->where('status', 'graded')->avg('score') : 0;

        return [
            'total_students' => $totalStudents,
            'submitted' => $submittedCount,
            'pending' => $totalStudents - $submittedCount,
            'late' => $lateCount,
            'graded' => $gradedCount,
            'average_score' => round($averageScore, 2),
        ];
    }
}
