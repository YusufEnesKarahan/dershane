<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQQuotaViolation extends Model
{
    use HasFactory;

    protected $table = 'hq_quota_violations';

    protected $fillable = [
        'tenant_id',
        'metric_key',
        'limit_value',
        'actual_value',
        'severity',
        'resolved_at',
    ];

    protected $casts = [
        'limit_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
