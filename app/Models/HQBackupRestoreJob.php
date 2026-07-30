<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQBackupRestoreJob extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_restore_jobs';

    protected $fillable = [
        'uuid',
        'target_instance_id',
        'hq_backup_snapshot_id',
        'type', // latest, specific, point_in_time
        'mode', // dry_run, validation, execute
        'status', // pending, running, completed, failed
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function targetInstance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'target_instance_id');
    }

    public function snapshot()
    {
        return $this->belongsTo(HQBackupSnapshot::class, 'hq_backup_snapshot_id');
    }
}
