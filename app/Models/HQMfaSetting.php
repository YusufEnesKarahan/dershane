<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class HQMfaSetting extends Model
{
    use HasFactory;

    protected $table = 'hq_mfa_settings';

    protected $fillable = [
        'user_id',
        'is_enabled',
        'secret',
        'recovery_codes',
        'backup_codes',
        'uuid',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'recovery_codes' => 'array',
        'backup_codes' => 'array',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
