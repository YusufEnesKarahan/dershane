<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQCommand extends Model
{
    use HasFactory;

    protected $table = 'hq_commands';

    protected $fillable = [
        'command_uuid',
        'command_type',
        'payload',
        'status',
        'result',
        'requested_at',
        'executed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'requested_at' => 'datetime',
        'executed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->command_uuid)) {
                $model->command_uuid = (string) Str::uuid();
            }
        });
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExecuted(): bool
    {
        return $this->status === 'executed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
