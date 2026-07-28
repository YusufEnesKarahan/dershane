<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQTelemetryLog extends Model
{
    use HasFactory;

    protected $table = 'hq_telemetry_logs';

    protected $fillable = [
        'uuid',
        'type',
        'payload',
        'status',
        'generated_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'generated_at' => 'datetime',
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
