<?php

namespace App\Domain\Communication\Services;

use App\Models\SmsProvider;
use App\Models\NotificationLog;

class SmsService
{
    public function sendSms(string $recipient, string $message): bool
    {
        $provider = SmsProvider::where('is_active', true)->first();
        $providerCode = $provider ? $provider->code : 'MockProvider';

        NotificationLog::create([
            'recipient' => $recipient,
            'channel' => 'SMS',
            'provider' => $providerCode,
            'status' => 'Sent',
        ]);

        return true;
    }
}
