<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQComplianceFramework extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_compliance_frameworks';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'version',
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

    public function controls()
    {
        return $this->hasMany(HQComplianceControl::class, 'framework_id');
    }

    public function results()
    {
        return $this->hasMany(HQComplianceResult::class, 'framework_id');
    }
}
