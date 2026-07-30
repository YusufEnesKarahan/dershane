<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQBackupSnapshot extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_snapshots';

    protected $fillable = [
        'uuid',
        'hq_backup_job_id',
        'type', // full, incremental, differential, metadata
        'path',
        'size_bytes',
        'checksum',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
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

    public function job()
    {
        return $this->belongsTo(HQBackupJob::class, 'hq_backup_job_id');
    }

    public function restores()
    {
        return $this->hasMany(HQBackupRestoreJob::class, 'hq_backup_snapshot_id');
    }
}
