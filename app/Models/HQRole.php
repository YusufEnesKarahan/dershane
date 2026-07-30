<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQRole extends Model
{
    use HasFactory;

    protected $table = 'hq_roles';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'is_system',
        'uuid',
    ];

    protected $casts = [
        'is_system' => 'boolean',
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

    public function permissions()
    {
        return $this->belongsToMany(HQPermission::class, 'hq_role_permissions', 'role_id', 'permission_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'hq_user_roles', 'role_id', 'user_id')->withTimestamps();
    }
}
