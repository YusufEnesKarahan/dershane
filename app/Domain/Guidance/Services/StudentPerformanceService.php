<?php

namespace App\Domain\Guidance\Services;

use App\Models\PerformanceSnapshot;
use App\Models\StudentRiskLevel;
use App\Models\Student;
use App\Models\User;
use App\Models\ExamResult;
use App\Models\HomeworkSubmission;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentPerformanceService
{
    /**
     * Calculate performance metrics and risk score, and generate a snapshot.
     */
    public function generateSnapshot(int $studentId, int $academicTermId): PerformanceSnapshot
    {
        $student = Student::findOrFail($studentId);

        // 1. Calculate Exam Average
        $examAverage = ExamResult::where('student_id', $studentId)
            ->whereHas('exam', function ($q) use ($academicTermId, $student) {
                // Exam model usually doesn't have academic_term_id directly, but we check if we can.
                // Assuming it might, or we just calculate all time for current term dates.
                $q->where('branch_id', $student->branch_id);
            })->avg('score') ?? 0;

        // 2. Calculate Homework Completion
        $totalHomeworks = \App\Models\Homework::where('branch_id', $student->branch_id)
            ->where('academic_term_id', $academicTermId)
            ->count();
            
        $completedHomeworks = HomeworkSubmission::where('student_id', $studentId)
            ->whereHas('homework', function ($q) use ($academicTermId) {
                $q->where('academic_term_id', $academicTermId);
            })->count();
            
        $lateSubmissions = HomeworkSubmission::where('student_id', $studentId)
            ->where('status', 'Late')
            ->whereHas('homework', function ($q) use ($academicTermId) {
                $q->where('academic_term_id', $academicTermId);
            })->count();

        $homeworkCompletionRate = $totalHomeworks > 0 ? ($completedHomeworks / $totalHomeworks) * 100 : 100;
        $lateSubmissionRate = $completedHomeworks > 0 ? ($lateSubmissions / $completedHomeworks) * 100 : 0;

        // 3. Calculate Attendance Rate
        // Assuming Attendance model exists. If not, fallback to 100
        $totalDays = class_exists(Attendance::class) ? Attendance::where('student_id', $studentId)->count() : 0;
        $presentDays = class_exists(Attendance::class) ? Attendance::where('student_id', $studentId)->where('status', 'Present')->count() : 0;
        $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 100;

        // 4. Calculate Risk Score
        $riskScore = $this->calculateRiskScore($attendanceRate, $examAverage, $homeworkCompletionRate, $lateSubmissionRate);

        // Create or update snapshot
        $snapshot = PerformanceSnapshot::create([
            'branch_id' => $student->branch_id,
            'student_id' => $studentId,
            'academic_term_id' => $academicTermId,
            'attendance_rate' => $attendanceRate,
            'exam_average' => $examAverage,
            'homework_completion' => $homeworkCompletionRate,
            'late_submission_rate' => $lateSubmissionRate,
            'risk_score' => $riskScore,
            'snapshot_date' => Carbon::now()->toDateString()
        ]);

        $this->updateRiskLevel($studentId, $riskScore, 'Generated from performance snapshot.');

        return $snapshot;
    }

    protected function calculateRiskScore($attendance, $exam, $hwCompletion, $lateRate): string
    {
        $riskPoints = 0;

        if ($attendance < 80) $riskPoints += 3;
        elseif ($attendance < 90) $riskPoints += 1;

        if ($exam < 50) $riskPoints += 3;
        elseif ($exam < 65) $riskPoints += 1;

        if ($hwCompletion < 50) $riskPoints += 2;
        elseif ($hwCompletion < 70) $riskPoints += 1;

        if ($lateRate > 50) $riskPoints += 1;

        if ($riskPoints >= 6) return 'Critical';
        if ($riskPoints >= 4) return 'High';
        if ($riskPoints >= 2) return 'Medium';
        return 'Low';
    }

    public function updateRiskLevel(int $studentId, string $level, string $reason): void
    {
        $student = Student::findOrFail($studentId);
        
        $currentLevel = StudentRiskLevel::where('student_id', $studentId)->orderBy('created_at', 'desc')->first();

        if (!$currentLevel || $currentLevel->level !== $level) {
            StudentRiskLevel::create([
                'branch_id' => $student->branch_id,
                'student_id' => $studentId,
                'level' => $level,
                'reason' => $reason,
                'updated_by' => auth()->id() ?? User::where('branch_id', $student->branch_id)->first()->id // Fallback for tests/jobs
            ]);
            
            if (in_array($level, ['High', 'Critical']) && (!$currentLevel || !in_array($currentLevel->level, ['High', 'Critical']))) {
                // Could fire event: event(new RiskLevelIncreased($student, $level));
            }
        }
    }
}
