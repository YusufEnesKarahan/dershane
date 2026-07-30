<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQBackupJob extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_jobs';

    protected $fillable = [
        'backup_policy_id',
        'system_instance_id',
        'status',
        'started_at',
        'finished_at',
        'size',
        'storage_location',
        'error_message',
        'metadata',
        'uuid',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'metadata' => 'array',
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

    public function policy()
    {
        return $this->belongsTo(HQBackupPolicy::class, 'backup_policy_id');
    }

    public function systemInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }

    public function logs()
    {
        return $this->hasMany(HQBackupLog::class, 'backup_job_id');
    }

    public function snapshots()
    {
        return $this->hasMany(HQBackupSnapshot::class, 'hq_backup_job_id');
    }
}
