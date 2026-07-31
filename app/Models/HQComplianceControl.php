<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQComplianceControl extends Model
{
    use HasFactory;

    protected $table = 'hq_compliance_controls';

    protected $fillable = [
        'uuid',
        'framework_id',
        'control_code',
        'title',
        'description',
        'policy_id',
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

    public function framework()
    {
        return $this->belongsTo(HQComplianceFramework::class, 'framework_id');
    }

    public function policy()
    {
        return $this->belongsTo(HQPolicy::class, 'policy_id');
    }
}
