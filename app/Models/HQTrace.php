<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQTrace extends Model
{
    use HasFactory;

    protected $table = 'hq_traces';
    public $timestamps = false;

    protected $fillable = [
        'trace_id',
        'tenant_id',
        'service_name',
        'operation',
        'duration_ms',
        'status',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
