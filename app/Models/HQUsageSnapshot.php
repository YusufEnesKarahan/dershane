<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQUsageSnapshot extends Model
{
    use HasFactory;

    protected $table = 'hq_usage_snapshots';

    protected $fillable = [
        'tenant_id',
        'period',
        'period_start',
        'period_end',
        'data_json',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'data_json' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
