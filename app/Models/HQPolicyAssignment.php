<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQPolicyAssignment extends Model
{
    use HasFactory;

    protected $table = 'hq_policy_assignments';

    protected $fillable = [
        'uuid',
        'policy_id',
        'tenant_id',
        'overrides',
    ];

    protected $casts = [
        'overrides' => 'array',
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

    public function policy()
    {
        return $this->belongsTo(HQPolicy::class, 'policy_id');
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
