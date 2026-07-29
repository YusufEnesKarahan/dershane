<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class HQConfigurationItem extends Model
{
    use HasFactory;

    protected $table = 'hq_configuration_items';

    protected $fillable = [
        'profile_id',
        'key',
        'value',
        'type',
        'is_sensitive',
        'sort_order',
    ];

    protected $casts = [
        'is_sensitive' => 'boolean',
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

    public function profile()
    {
        return $this->belongsTo(HQConfigurationProfile::class, 'profile_id');
    }

    /**
     * Set the value, optionally encrypting it.
     * Note: type is saved alongside it.
     */
    public function setValueAttribute($value)
    {
        if ($this->attributes['type'] ?? 'string' === 'encrypted' || !empty($this->attributes['is_sensitive'])) {
            $this->attributes['value'] = $value !== null ? Crypt::encryptString($value) : null;
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get the decrypted value if encrypted.
     */
    public function getDecryptedValueAttribute()
    {
        if (($this->type === 'encrypted' || $this->is_sensitive) && $this->value !== null) {
            try {
                return Crypt::decryptString($this->value);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return $this->value;
    }
}
