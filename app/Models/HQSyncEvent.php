<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSyncEvent extends Model
{
    use HasFactory;

    protected $table = 'hq_sync_queue';

    protected $fillable = [
        'event_type',
        'payload',
        'status',
        'retry_count',
        'last_error',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
