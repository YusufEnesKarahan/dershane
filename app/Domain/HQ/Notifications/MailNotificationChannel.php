<?php

namespace App\Domain\HQ\Notifications;

use App\Domain\HQ\Contracts\NotificationChannelInterface;
use App\Models\HQAlert;
use App\Models\HQNotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailNotificationChannel implements NotificationChannelInterface
{
    public function send(HQAlert $alert): bool
    {
        // For testing/development, we'll assume a configurable HQ admin email or a fallback
        $recipient = config('hq.admin_email', 'admin@example.com');

        try {
            // Simplified mail sending. In production you would use a Mailable class.
            Mail::raw("HQ Alert: {$alert->title}\n\nSeverity: {$alert->severity}\n\nMessage: {$alert->message}", function ($message) use ($recipient, $alert) {
                $message->to($recipient)
                        ->subject("[HQ Central] [{$alert->severity}] {$alert->title}");
            });

            HQNotificationLog::create([
                'alert_id' => $alert->id,
                'channel' => 'mail',
                'recipient' => $recipient,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("MailNotificationChannel failed to send email: " . $e->getMessage());
            
            HQNotificationLog::create([
                'alert_id' => $alert->id,
                'channel' => 'mail',
                'recipient' => $recipient,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
}
