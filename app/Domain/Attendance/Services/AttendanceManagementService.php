<?php

namespace App\Domain\Attendance\Services;

use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;
use App\Domain\Tenant\Services\SubscriptionLimitService;

class AttendanceManagementService
{
    public function __construct(
        protected SubscriptionLimitService $limitService
    ) {}

    public function createSession(array $data): AttendanceSession
    {
        $this->limitService->checkAttendanceLimit($data['branch_id']);

        return AttendanceSession::create([
            'branch_id' => $data['branch_id'],
            'classroom_id' => $data['classroom_id'],
            'lesson_schedule_id' => $data['lesson_schedule_id'] ?? null,
            'teacher_id' => $data['teacher_id'],
            'session_date' => $data['session_date'],
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'status' => 'open'
        ]);
    }

    public function markStudentAttendance(array $data): AttendanceRecord
    {
        // Prevent duplicates
        $existing = AttendanceRecord::where('branch_id', $data['branch_id'])
            ->where('student_id', $data['student_id'])
            ->whereDate('attendance_date', $data['attendance_date'])
            ->where('attendance_session_id', $data['attendance_session_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->update($data);
            $record = $existing;
        } else {
            $record = AttendanceRecord::create($data);
        }

        if (in_array(strtolower($data['status']), ['absent', 'devamsiz'])) {
            $student = \App\Models\Student::find($data['student_id']);
            if ($student) {
                $notificationService = app(\App\Domain\Notification\Services\NotificationService::class);
                $guardians = \App\Models\StudentGuardian::where('student_id', $student->id)->get();
                foreach ($guardians as $guardian) {
                    $notificationService->sendToParent(
                        $guardian,
                        'Devamsızlık Bildirimi',
                        "Öğrenciniz {$student->first_name} {$student->last_name} bugün devamsızlık yaptı.",
                        'attendance'
                    );
                }
            }
        }

        return $record;
    }

    public function bulkMarkAttendance(AttendanceSession $session, array $records, int $createdBy)
    {
        return DB::transaction(function () use ($session, $records, $createdBy) {
            foreach ($records as $record) {
                $this->markStudentAttendance([
                    'branch_id' => $session->branch_id,
                    'attendance_session_id' => $session->id,
                    'student_id' => $record['student_id'],
                    'classroom_id' => $session->classroom_id,
                    'lesson_schedule_id' => $session->lesson_schedule_id,
                    'teacher_id' => $session->teacher_id,
                    'attendance_date' => $session->session_date,
                    'status' => $record['status'],
                    'note' => $record['note'] ?? null,
                    'created_by' => $createdBy
                ]);
            }
            
            $session->update(['status' => 'completed']);
            return true;
        });
    }

    public function completeSession(AttendanceSession $session)
    {
        $session->update(['status' => 'completed']);
        return $session;
    }
    
    public function cancelSession(AttendanceSession $session)
    {
        return DB::transaction(function () use ($session) {
            $session->records()->delete();
            $session->update(['status' => 'cancelled']);
            return $session;
        });
    }

    public function updateAttendance(AttendanceRecord $record, array $data)
    {
        $record->update($data);
        return $record;
    }
}
