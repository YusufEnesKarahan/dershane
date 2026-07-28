<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSyncLog extends Model
{
    use HasFactory;

    protected $table = 'hq_sync_logs';
    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'request_url',
        'request_method',
        'request_payload',
        'response_status',
        'response_payload',
        'duration_ms',
        'success',
        'created_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'success' => 'boolean',
        'created_at' => 'datetime',
    ];
}
