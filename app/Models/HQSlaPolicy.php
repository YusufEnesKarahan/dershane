<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQSlaPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_sla_policies';

    protected $fillable = [
        'uuid',
        'name',
        'metric',
        'operator',
        'threshold_value',
        'evaluation_period_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function violations()
    {
        return $this->hasMany(HQSlaViolation::class, 'sla_policy_id');
    }
}
