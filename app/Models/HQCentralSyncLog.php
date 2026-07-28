<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQCentralSyncLog extends Model
{
    use HasFactory;

    protected $table = 'hq_central_sync_logs';
    public $timestamps = false;

    protected $fillable = [
        'system_instance_id',
        'event',
        'payload',
        'response',
        'status',
        'duration_ms',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'created_at' => 'datetime',
    ];

    public function instance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }
}
