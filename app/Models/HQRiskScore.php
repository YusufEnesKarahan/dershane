<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQRiskScore extends Model
{
    use HasFactory;

    protected $table = 'hq_risk_scores';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'score',
        'level',
        'factors',
        'calculated_at',
    ];

    protected $casts = [
        'factors' => 'array',
        'calculated_at' => 'datetime',
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
