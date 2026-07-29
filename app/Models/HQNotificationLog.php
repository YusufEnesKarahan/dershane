<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQNotificationLog extends Model
{
    use HasFactory;

    protected $table = 'hq_notification_logs';

    protected $fillable = [
        'alert_id',
        'channel',
        'recipient',
        'status',
        'sent_at',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function alert()
    {
        return $this->belongsTo(HQAlert::class, 'alert_id');
    }
}
