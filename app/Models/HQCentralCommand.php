<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQCentralCommand extends Model
{
    use HasFactory;

    protected $table = 'hq_central_commands';

    protected $fillable = [
        'system_instance_id',
        'command_type',
        'payload',
        'status',
        'priority',
        'scheduled_at',
        'executed_at',
        'expires_at',
        'retry_count',
        'max_retry',
        'response',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function instance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }
}
