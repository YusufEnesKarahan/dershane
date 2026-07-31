<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HQPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_policies';

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'type',
        'severity',
        'is_active',
        'logic',
        'created_by',
    ];

    protected $casts = [
        'logic' => 'array',
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

    public function rules()
    {
        return $this->hasMany(HQPolicyRule::class, 'policy_id');
    }

    public function assignments()
    {
        return $this->hasMany(HQPolicyAssignment::class, 'policy_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
