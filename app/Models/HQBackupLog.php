<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQBackupLog extends Model
{
    use HasFactory;

    protected $table = 'hq_backup_logs';
    public $timestamps = false; // we only have created_at

    protected $fillable = [
        'backup_job_id',
        'action',
        'payload',
        'response',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = $model->freshTimestamp();
            }
        });
    }

    public function backupJob()
    {
        return $this->belongsTo(HQBackupJob::class, 'backup_job_id');
    }
}
