<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingGroup extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'sort_order'];

    public function settings()
    {
        return $this->hasMany(PlatformSetting::class, 'group_id')->orderBy('sort_order');
    }
}
