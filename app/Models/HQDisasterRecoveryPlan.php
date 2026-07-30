<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQDisasterRecoveryPlan extends Model
{
    use HasFactory;

    protected $table = 'hq_disaster_recovery_plans';

    protected $fillable = [
        'uuid',
        'tenant_id',
        'name',
        'description',
        'priority', // high, medium, low
        'dependencies',
        'status', // active, inactive, testing, running
        'last_run_at',
    ];

    protected $casts = [
        'dependencies' => 'array',
        'last_run_at' => 'datetime',
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
        return $this->belongsTo(HQTenant::class);
    }
}
