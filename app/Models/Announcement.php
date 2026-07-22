<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'announcement_group_id', 'is_published', 'is_active', 'published_at'];

    public function group()
    {
        return $this->belongsTo(AnnouncementGroup::class, 'announcement_group_id');
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }
}
