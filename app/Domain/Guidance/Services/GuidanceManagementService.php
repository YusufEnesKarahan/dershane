<?php

namespace App\Domain\Guidance\Services;

use App\Models\StudentGuidanceRecord;
use App\Models\StudentMeeting;
use App\Models\ParentMeeting;
use App\Models\StudentGoal;
use App\Models\StudentNote;
use App\Domain\Tenant\Services\SubscriptionLimitService;
use App\Domain\Notification\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class GuidanceManagementService
{
    public function __construct(
        protected SubscriptionLimitService $limitService,
        protected NotificationService $notificationService,
        protected StudentPerformanceService $performanceService
    ) {}

    public function createRecord(array $data): StudentGuidanceRecord
    {
        $this->limitService->checkGuidanceLimit($data['branch_id']);

        return DB::transaction(function () use ($data) {
            return StudentGuidanceRecord::create($data);
        });
    }

    public function updateRecord(StudentGuidanceRecord $record, array $data): StudentGuidanceRecord
    {
        $record->update($data);
        return $record;
    }

    public function scheduleMeeting(array $data): StudentMeeting
    {
        return DB::transaction(function () use ($data) {
            $meeting = StudentMeeting::create($data);
            
            // Notify Student
            if ($meeting->student && $meeting->student->user) {
                $this->notificationService->send(
                    $meeting->student->user,
                    'Yeni Rehberlik Toplantısı',
                    "Sizinle yeni bir rehberlik toplantısı planlandı: {$meeting->meeting_date->format('d.m.Y H:i')}",
                    \App\Domain\Notification\Enums\NotificationType::SYSTEM
                );
            }

            return $meeting;
        });
    }

    public function scheduleParentMeeting(array $data): ParentMeeting
    {
        return DB::transaction(function () use ($data) {
            $meeting = ParentMeeting::create($data);
            
            // Notify Parent
            if ($meeting->guardian && $meeting->guardian->user) {
                $this->notificationService->send(
                    $meeting->guardian->user,
                    'Veli Görüşmesi Planlandı',
                    "Öğrenciniz için yeni bir görüşme planlandı: {$meeting->meeting_date->format('d.m.Y H:i')}",
                    \App\Domain\Notification\Enums\NotificationType::SYSTEM
                );
            }

            return $meeting;
        });
    }

    public function createGoal(array $data): StudentGoal
    {
        return DB::transaction(function () use ($data) {
            return StudentGoal::create($data);
        });
    }

    public function updateGoalProgress(StudentGoal $goal, string $currentValue, string $status): StudentGoal
    {
        return DB::transaction(function () use ($goal, $currentValue, $status) {
            $goal->update([
                'current_value' => $currentValue,
                'status' => $status
            ]);
            
            if ($status === 'Achieved' && $goal->student && $goal->student->user) {
                $this->notificationService->send(
                    $goal->student->user,
                    'Hedefe Ulaşıldı! 🌟',
                    "Tebrikler! '{$goal->title}' hedefinize ulaştınız.",
                    \App\Domain\Notification\Enums\NotificationType::SYSTEM
                );
            }

            return $goal;
        });
    }

    public function addNote(array $data): StudentNote
    {
        return StudentNote::create($data);
    }
}
