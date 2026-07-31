<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQMetric extends Model
{
    use HasFactory;

    protected $table = 'hq_metrics';
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'tenant_id',
        'metric_name',
        'metric_type',
        'value',
        'unit',
        'tags',
        'recorded_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'value' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
