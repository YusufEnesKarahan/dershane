<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQTelemetryRecord extends Model
{
    use HasFactory;

    protected $table = 'hq_telemetry_records';

    protected $fillable = [
        'system_instance_id',
        'type',
        'payload',
        'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    public function instance()
    {
        return $this->belongsTo(HQSystemInstance::class, 'system_instance_id');
    }
}
