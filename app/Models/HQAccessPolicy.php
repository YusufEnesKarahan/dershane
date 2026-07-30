<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQAccessPolicy extends Model
{
    use HasFactory;

    protected $table = 'hq_access_policies';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'ip_restrictions',
        'time_restrictions',
        'resource_restrictions',
        'is_active',
        'uuid',
    ];

    protected $casts = [
        'ip_restrictions' => 'array',
        'time_restrictions' => 'array',
        'resource_restrictions' => 'array',
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

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class, 'tenant_id');
    }
}
