<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQMetricSnapshot extends Model
{
    use HasFactory;

    protected $table = 'hq_metric_snapshots';
    public $timestamps = false;

    protected $fillable = [
        'metric_name',
        'aggregation_type',
        'value',
        'period',
        'snapshot_date',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'snapshot_date' => 'datetime',
    ];
}
