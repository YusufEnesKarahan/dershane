<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingFile extends Model
{
    protected $fillable = ['setting_id', 'disk', 'filename', 'original_name', 'mime_type', 'size'];

    public function setting()
    {
        return $this->belongsTo(PlatformSetting::class, 'setting_id');
    }
}
