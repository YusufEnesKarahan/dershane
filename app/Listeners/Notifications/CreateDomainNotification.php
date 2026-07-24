<?php
namespace App\Listeners\Notifications;

use App\DTOs\Notification\CreateNotificationDTO;
use App\Domain\Notification\Services\NotificationService;
use App\Events\Notifications\{StudentRegistered, PaymentOverdue, ExamResultPublished, HomeworkAssigned, CrmFollowupDue};
use App\Models\User;

class CreateDomainNotification
{
    public function __construct(private readonly NotificationService $notifications) {}
    public function handle(object $event): void
    {
        [$title, $message, $type, $data] = match (true) {
            $event instanceof StudentRegistered => ['Yeni öğrenci kaydı', $event->student->full_name.' adlı öğrenci kaydedildi.', 'student', ['student_id' => $event->student->id]],
            $event instanceof PaymentOverdue => ['Ödeme gecikmesi', $event->invoice->invoice_number.' numaralı fatura gecikmede.', 'finance', ['invoice_id' => $event->invoice->id]],
            $event instanceof ExamResultPublished => ['Yeni sınav sonucu', 'Yeni sınav sonucu kaydedildi.', 'student', ['exam_result_id' => $event->result->id]],
            $event instanceof HomeworkAssigned => ['Yeni ödev', $event->assignment->title.' ödevi yayınlandı.', 'student', ['assignment_id' => $event->assignment->id]],
            $event instanceof CrmFollowupDue => ['CRM takip zamanı', $event->activity->description, 'crm', ['lead_id' => $event->activity->lead_id]],
            default => [null, null, 'system', []],
        };
        if (!$title) return;
        User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Administrator'))->select('id')->each(fn (User $user) => $this->notifications->create(new CreateNotificationDTO($user->id, $title, $message, $type, 'panel', 'normal', $data)));
    }
}
