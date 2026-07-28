<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQSchedulerLog extends Model
{
    use HasFactory;

    protected $table = 'hq_scheduler_logs';

    protected $fillable = [
        'uuid',
        'task_name',
        'status',
        'started_at',
        'finished_at',
        'duration_ms',
        'result',
        'error_message',
    ];

    protected $casts = [
        'result' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
