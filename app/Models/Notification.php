<?php

namespace App\Models;

use App\Core\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'branch_id',
        'sender_id',
        'receiver_id',
        'receiver_type',
        'user_id',
        'type',
        'title',
        'message',
        'content',
        'data',
        'channel',
        'priority',
        'status',
        'read_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'sent_at' => 'datetime'
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null || $this->status === 'Read';
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp(), 'status' => 'Read'])->save();
        }
    }
}
