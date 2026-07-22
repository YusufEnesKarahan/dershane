<?php

namespace App\Domain\Communication\Services;

use App\Models\MailTemplate;
use App\Models\NotificationLog;

class MailService
{
    public function sendEmail(string $recipient, string $subject, string $body): bool
    {
        NotificationLog::create([
            'recipient' => $recipient,
            'channel' => 'Email',
            'provider' => 'SMTP',
            'status' => 'Sent',
        ]);

        return true;
    }
}
