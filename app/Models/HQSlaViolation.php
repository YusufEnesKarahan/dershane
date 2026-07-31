<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQSlaViolation extends Model
{
    use HasFactory;

    protected $table = 'hq_sla_violations';

    protected $fillable = [
        'sla_policy_id',
        'tenant_id',
        'actual_value',
        'status',
        'detected_at',
        'resolved_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function policy()
    {
        return $this->belongsTo(HQSlaPolicy::class, 'sla_policy_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
