<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'content', 'data', 'channel', 'priority',
        'status', 'read_at', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['data' => 'array', 'read_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null || $this->status === 'Read';
    }
}
