<?php

namespace App\Domain\HQ\Contracts;

use App\Models\HQAlert;

interface NotificationChannelInterface
{
    /**
     * Send the alert notification.
     *
     * @param HQAlert $alert The alert to send
     * @return bool True if successful, false otherwise
     */
    public function send(HQAlert $alert): bool;
}
