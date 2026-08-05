<?php

namespace App\Domain\Academic\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamBranchResult;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Branch;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Support\Facades\DB;

class AcademicProfessionalService
{
    public const BRANCH_LIST = [
        'Türkçe', 'Matematik', 'Fen', 'Sosyal', 'Geometri',
        'Fizik', 'Kimya', 'Biyoloji', 'Tarih', 'Coğrafya',
        'Din', 'Felsefe', 'İngilizce'
    ];

    /**
     * Calculate and store 13 branch results for an ExamResult
     */
    public function saveExamBranchResults(ExamResult $examResult, array $branchesData, string $examType = 'TYT'): void
    {
        DB::transaction(function () use ($examResult, $branchesData, $examType) {
            $totalCorrect = 0;
            $totalWrong = 0;
            $totalEmpty = 0;
            $totalNet = 0.0;

            $divisor = (strtoupper($examType) === 'LGS') ? 3.0 : 4.0;

            foreach ($branchesData as $branchName => $data) {
                $correct = (int) ($data['correct'] ?? 0);
                $wrong = (int) ($data['wrong'] ?? 0);
                $empty = (int) ($data['empty'] ?? 0);
                $net = round($correct - ($wrong / $divisor), 2);
                if ($net < 0) $net = 0.0;

                $totalCorrect += $correct;
                $totalWrong += $wrong;
                $totalEmpty += $empty;
                $totalNet += $net;

                ExamBranchResult::updateOrCreate(
                    [
                        'exam_result_id' => $examResult->id,
                        'branch_name' => $branchName,
                    ],
                    [
                        'correct_count' => $correct,
                        'wrong_count' => $wrong,
                        'empty_count' => $empty,
                        'net_count' => $net,
                    ]
                );
            }

            $examResult->update([
                'correct_answers' => $totalCorrect,
                'wrong_answers' => $totalWrong,
                'empty_answers' => $totalEmpty,
                'total_net' => $totalNet,
            ]);
        });
    }

    /**
     * Get Student Net Growth over recent practice exams
     */
    public function getStudentNetGrowth(int $studentId): array
    {
        $results = ExamResult::where('student_id', $studentId)
            ->with(['exam', 'branchResults'])
            ->orderBy('created_at', 'asc')
            ->get();

        $labels = [];
        $netData = [];
        $scoreData = [];

        foreach ($results as $res) {
            $labels[] = $res->exam?->title ?? ('Sınav #' . $res->exam_id);
            $netData[] = (float) $res->total_net;
            $scoreData[] = (float) $res->score;
        }

        return [
            'labels' => $labels,
            'net_series' => $netData,
            'score_series' => $scoreData,
            'total_exams' => $results->count(),
            'average_net' => $results->count() > 0 ? round($results->avg('total_net'), 2) : 0,
            'latest_net' => $results->last()?->total_net ?? 0,
        ];
    }

    /**
     * Compare Student vs Class vs Branch vs Institution Averages
     */
    public function getComparisonMetrics(int $studentId, int $branchId, ?int $classroomId = null): array
    {
        $studentAvgNet = ExamResult::where('student_id', $studentId)->avg('total_net') ?? 0;
        
        $classAvgNet = 0;
        if ($classroomId) {
            $studentIds = Student::where('classroom_id', $classroomId)->pluck('id');
            $classAvgNet = ExamResult::whereIn('student_id', $studentIds)->avg('total_net') ?? 0;
        }

        $branchAvgNet = ExamResult::where('branch_id', $branchId)->avg('total_net') ?? 0;
        $institutionAvgNet = ExamResult::avg('total_net') ?? 0;

        return [
            'student_avg' => round((float) $studentAvgNet, 2),
            'class_avg' => round((float) $classAvgNet, 2),
            'branch_avg' => round((float) $branchAvgNet, 2),
            'institution_avg' => round((float) $institutionAvgNet, 2),
        ];
    }

    /**
     * Calculate Student Weekly Study Program Completion Stats for Student/Parent panels
     */
    public function getStudentStudyProgramSummary(int $studentId): array
    {
        $student = Student::find($studentId);
        $classroomId = $student?->classroom_id;

        $query = Homework::query();
        if ($classroomId) {
            $query->where(function ($q) use ($classroomId, $studentId) {
                $q->where('classroom_id', $classroomId)
                  ->orWhereHas('submissions', function ($sq) use ($studentId) {
                      $sq->where('student_id', $studentId);
                  });
            });
        }

        $schedules = $query->with(['submissions' => function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        }, 'teacher.user', 'course'])->orderBy('start_date', 'desc')->limit(10)->get();

        $totalTasks = 0;
        $completedTasks = 0;
        $inProgressTasks = 0;

        foreach ($schedules as $sch) {
            $sub = $sch->submissions->first();
            $totalTasks++;
            $status = $sub?->task_status ?? 'Not Started';
            if ($status === 'Completed' || $sub?->status === 'graded') {
                $completedTasks++;
            } elseif ($status === 'In Progress') {
                $inProgressTasks++;
            }
        }

        $progressPercentage = $totalTasks > 0 ? (int) round(($completedTasks / $totalTasks) * 100) : 0;

        return [
            'schedules' => $schedules,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'progress_percentage' => $progressPercentage,
        ];
    }
}
