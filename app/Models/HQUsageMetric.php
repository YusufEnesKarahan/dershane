<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQUsageMetric extends Model
{
    use HasFactory;

    protected $table = 'hq_usage_metrics';

    protected $fillable = [
        'tenant_id',
        'metric_key',
        'metric_value',
        'reported_at',
    ];

    protected $casts = [
        'metric_value' => 'decimal:2',
        'reported_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
