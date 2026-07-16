<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class PlatformSetting extends Model
{
    protected $fillable = ['group_id', 'key', 'value', 'type', 'is_encrypted', 'validation_rules', 'sort_order'];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(SettingGroup::class, 'group_id');
    }

    public function histories()
    {
        return $this->hasMany(SettingHistory::class, 'setting_id');
    }

    public function files()
    {
        return $this->hasMany(SettingFile::class, 'setting_id');
    }

    // Accessor: Decrypt if needed
    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }
        return $value;
    }

    // Mutator: Encrypt if needed
    public function setValueAttribute($value)
    {
        // Audit trail trigger
        if ($this->exists && $this->getOriginal('value') !== $value) {
            SettingHistory::create([
                'setting_id' => $this->id,
                'old_value' => $this->getOriginal('value'),
                'new_value' => $value,
                'changed_by' => Auth::id(),
            ]);
        }

        if ($this->is_encrypted && !empty($value)) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
