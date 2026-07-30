<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQPermission extends Model
{
    use HasFactory;

    protected $table = 'hq_permissions';

    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
        'uuid',
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

    public function roles()
    {
        return $this->belongsToMany(HQRole::class, 'hq_role_permissions', 'permission_id', 'role_id')->withTimestamps();
    }
}
