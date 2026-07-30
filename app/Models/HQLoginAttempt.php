<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQLoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'hq_login_attempts';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'is_successful',
        'attempted_at',
        'context',
        'uuid',
    ];

    protected $casts = [
        'is_successful' => 'boolean',
        'attempted_at' => 'datetime',
        'context' => 'array',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
