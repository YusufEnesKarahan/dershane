<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQUsageRecord extends Model
{
    use HasFactory;

    protected $table = 'hq_usage_records';

    protected $fillable = [
        'tenant_id',
        'metric_name',
        'value',
        'period',
    ];

    protected $casts = [
        'value' => 'decimal:4',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
