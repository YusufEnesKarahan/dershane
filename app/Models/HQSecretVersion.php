<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HQSecretVersion extends Model
{
    use HasFactory;

    protected $table = 'hq_secret_versions';

    protected $fillable = [
        'uuid',
        'secret_vault_id',
        'encrypted_value',
        'created_by',
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

    public function vault()
    {
        return $this->belongsTo(HQSecretVault::class, 'secret_vault_id');
    }
}
