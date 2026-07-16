<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingHistory extends Model
{
    protected $fillable = ['setting_id', 'old_value', 'new_value', 'changed_by'];

    public function setting()
    {
        return $this->belongsTo(PlatformSetting::class, 'setting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
