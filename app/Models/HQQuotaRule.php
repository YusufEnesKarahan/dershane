<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQQuotaRule extends Model
{
    use HasFactory;

    protected $table = 'hq_quota_rules';

    protected $fillable = [
        'tenant_id',
        'metric_key',
        'warning_threshold',
        'critical_threshold',
        'is_active',
    ];

    protected $casts = [
        'warning_threshold' => 'decimal:2',
        'critical_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
