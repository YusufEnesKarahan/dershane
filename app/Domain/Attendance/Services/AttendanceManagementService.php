<?php

namespace App\Domain\Attendance\Services;

use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\AttendanceStatus;
use Illuminate\Support\Facades\DB;
use App\Domain\Teacher\Services\TeacherPortalService;

class AttendanceManagementService
{
    public function __construct(protected TeacherPortalService $portalService) {}

    /**
     * Create a new attendance session.
     */
    public function createSession(array $data, int $branchId): AttendanceSession
    {
        return DB::transaction(function () use ($data, $branchId) {
            $session = new AttendanceSession();
            $session->fill($data);
            $session->branch_id = $branchId;
            $session->save();

            return $session;
        });
    }

    /**
     * Take or update bulk attendance for a session.
     */
    public function takeAttendance(int $sessionId, array $records, ?int $teacherId = null): void
    {
        $session = AttendanceSession::findOrFail($sessionId);

        // Security check for teacher
        if ($teacherId !== null) {
            if ($session->teacher_id !== $teacherId) {
                abort(403, 'Bu yoklama oturumuna erişim yetkiniz yok.');
            }
            abort_unless(
                $this->portalService->canManageClassCourse($teacherId, $session->classroom_id, $session->course_id),
                403,
                'Bu ders için yetkiniz bulunmuyor.'
            );
        }

        $studentIds = array_map('intval', array_keys($records));
        
        // Security check for students (must belong to the session's classroom)
        $allowedStudentCount = Student::query()
            ->where('branch_id', $session->branch_id)
            ->whereHas('classrooms', fn ($query) => $query->where('classrooms.id', $session->classroom_id))
            ->whereIn('id', $studentIds)
            ->count();
            
        abort_unless($allowedStudentCount === count(array_unique($studentIds)), 403, 'Sınıfa ait olmayan öğrenciler için yoklama alınamaz.');

        $statuses = AttendanceStatus::pluck('id', 'code')->mapWithKeys(fn($id, $code) => [strtoupper($code) => $id])->toArray();
        
        $upsertData = [];
        $now = now();

        foreach ($records as $studentId => $statusInput) {
            // handle both string code or array with status and remarks
            $statusCode = is_array($statusInput) ? ($statusInput['attendance_status_id'] ?? $statusInput['status'] ?? 'P') : $statusInput;
            $remarks = is_array($statusInput) ? ($statusInput['remarks'] ?? null) : null;
            
            // if it's already an ID, use it, otherwise fallback to code lookup
            if (is_numeric($statusCode)) {
                $statusId = (int) $statusCode;
            } else {
                $statusId = $statuses[strtoupper($statusCode)] ?? 1; // Default to 'P' (Present)
            }

            $upsertData[] = [
                'attendance_session_id' => $sessionId,
                'student_id' => (int) $studentId,
                'attendance_status_id' => $statusId,
                'remarks' => $remarks,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($upsertData)) {
            DB::transaction(function () use ($upsertData) {
                Attendance::upsert(
                    $upsertData,
                    ['attendance_session_id', 'student_id'],
                    ['attendance_status_id', 'remarks', 'updated_at']
                );
            });
        }
    }

    /**
     * Generate attendance reports.
     */
    public function generateReports(int $branchId, array $filters = []): array
    {
        $query = Attendance::with(['student', 'status', 'session.course'])
            ->whereHas('session', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });

        if (!empty($filters['start_date'])) {
            $query->whereHas('session', fn ($q) => $q->whereDate('session_date', '>=', $filters['start_date']));
        }

        if (!empty($filters['end_date'])) {
            $query->whereHas('session', fn ($q) => $q->whereDate('session_date', '<=', $filters['end_date']));
        }
        
        if (!empty($filters['classroom_id'])) {
            $query->whereHas('session', fn ($q) => $q->where('classroom_id', $filters['classroom_id']));
        }

        $records = $query->get();

        $summary = [
            'total' => $records->count(),
            'present' => $records->where('status.code', 'P')->count(),
            'absent' => $records->where('status.code', 'A')->count(),
            'late' => $records->where('status.code', 'L')->count(),
        ];
        
        if ($summary['total'] > 0) {
            $summary['attendance_rate'] = round((($summary['present'] + $summary['late']) / $summary['total']) * 100, 2);
        } else {
            $summary['attendance_rate'] = 0;
        }

        return [
            'summary' => $summary,
            'records' => $records
        ];
    }
}
