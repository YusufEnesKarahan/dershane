<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\DatabaseChannel;

class CustomDatabaseChannel extends DatabaseChannel
{
    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);

        return [
            'id' => $notification->id,
            'type' => get_class($notification),
            'data' => $data,
            'read_at' => null,
            'title' => $data['title'] ?? null,
            'message' => $data['content'] ?? null,
            'content' => $data['content'] ?? null,
            'channel' => 'database',
            'priority' => 'normal',
        ];
    }
}
