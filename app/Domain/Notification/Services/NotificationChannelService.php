<?php

namespace App\Domain\Notification\Services;

use App\Core\Repositories\Interfaces\NotificationLogRepositoryInterface;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class NotificationChannelService
{
    public function __construct(private readonly NotificationLogRepositoryInterface $logs) {}

    public function send(Notification $notification, string $channel): void
    {
        $user = $notification->user;
        $status = 'Sent';
        $error = null;

        try {
            if ($channel === 'email' && $user?->email) {
                // Queue-aware deployments may replace this adapter with a dedicated Mailable.
                Mail::raw($notification->message, fn ($mail) => $mail->to($user->email)->subject($notification->title));
            }
            // SMS is intentionally a provider-independent placeholder until a vendor adapter is configured.
        } catch (\Throwable $exception) {
            $status = 'Failed';
            $error = $exception->getMessage();
        }

        $this->logs->create([
            'notification_id' => $notification->id,
            'recipient' => $user?->email ?? ('User ID: '.$notification->user_id),
            'channel' => $channel,
            'provider' => match ($channel) { 'email' => 'Laravel Mail', 'sms' => 'SMS Placeholder', default => 'Panel' },
            'status' => $status,
            'error_message' => $error,
            'sent_at' => now(),
        ]);

        if ($status === 'Sent') $notification->update(['sent_at' => now()]);
    }
}
