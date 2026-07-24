<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = ['notification_id', 'recipient', 'channel', 'provider', 'status', 'error_message', 'sent_at'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
