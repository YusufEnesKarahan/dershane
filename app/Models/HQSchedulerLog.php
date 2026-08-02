<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HQSchedulerLog extends Model
{
    protected $table = 'hq_scheduler_logs';

    protected $fillable = [
        'task_name',
        'status',
        'started_at',
        'finished_at',
        'duration_ms',
        'result',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'result' => 'array',
        ];
    }
}