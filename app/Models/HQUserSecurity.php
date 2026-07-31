<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HQUserSecurity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hq_users_security';

    protected $fillable = [
        'uuid',
        'user_id',
        'tenant_id',
        'last_login_at',
        'last_ip',
        'failed_attempts',
        'locked_until',
        'mfa_enabled',
        'metadata',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'mfa_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(HQTenant::class);
    }
}
